<?php

require("./repos/mturk-php/mturk.php");

function logging($mes)
{
	$f = fopen("./logging.txt", "a");
	fwrite($f, "[ Time: " . get_time() . " Assignment ID: " . $_SESSION["assignmentId"] . " Worker ID: " . $_SESSION["workerId"] . " ]  " . $mes . "\n");
	fclose($f);
}

function store_url()
{
	logging("store_url called");

	$log = fopen("./urls/" . get_time() . ".json", "w");
	fwrite($log, json_encode($_SERVER) . "\n\n" . json_encode($_GET) . "\n\n" . json_encode($_POST));
	fclose($log);
}

function get_time()
{
	date_default_timezone_set("America/New_York");
        return date("Y-m-d H:i:s");
}

function httpPost($url, $data)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function get_points($phase, $sequence, $answer)
{
	logging("get_points called with " . $phase . " " . $sequence . " " . $answer);

	if(!session_id())
		session_start();

	if(!isset($_SESSION["testing_data"]))
		return 0;

	$arr = $_SESSION["testing_data"][$phase][$sequence];
        sort($arr);

        $a = intval($answer);

        $p = 0;

        if($arr[0] == $a)
                $p = 3;
	else if($arr[1] == $a)
	        $p = 2;
	else if($arr[2] == $a || $arr[3] ==$a || $arr[4] ==$a)
		$p = 1;

	return $p;
}


// returns an integer for the number of cents
function get_bonus($points)
{
	logging("get_bonus called with " . $points);

	$percent = round(100 * ($points / 300));
	$b = intval(round(4 * $percent));

	if($b > 400)
		return 400;
	else if($b < 0)
		return 0;
	else
		return $b;
}

function grant_bonuses()
{
	logging("grant_bonuses called");

	$c = dbConnect();

	$r = dbQuery($c, "SELECT * FROM responses WHERE bonus_paid=FALSE AND end_time < (NOW() - INTERVAL 10 MINUTE)");

	if(!empty($r))
	{
		foreach($r as $row)
		{
			grant_bonus($row["bonus"], $row["worker_id"], $row["assignment_id"]);

			dbQuery($c, "UPDATE responses SET bonus_paid=TRUE WHERE RID=:rid", ["rid" => $row["RID"]]);
		}
	}
}

function grant_bonus($b, $worker_id, $assignment_id)
{
	logging("grant_bonus called with " . $b . " " . $worker_id . " " . $assignment_id);

	//$b = get_bonus(intval($arr["points_phase0"]) + intval($arr["points_phase1"])) / 100;

	// b is inputted as an int of cents
	$bonus = $b / 100;

	if($bonus > 4)
		$bonus = 4;
	else if($bonus < 0)
		$bonus = 0;

	//echo "bonus: " . $bonus;

	$m = new MechanicalTurk();
	$r = $m->request('GrantBonus', array(
		"WorkerId" => $worker_id,
		"AssignmentId" => $assignment_id,
		"BonusAmount" => array(array("Amount" => $bonus, "CurrencyCode" => "USD")),
		"Reason" => "Thanks!"
	));

	$f = fopen("./bonus/" . $worker_id . ".json", "w");
	fwrite($f, json_encode([ "bonus" => $bonus, "worker_id" => $worker_id, "assignment_id" => $assignment_id, "result" => $r]));
	fclose($f);

	//var_dump($r);

	//httpPost("https://www.mturk.com/mturk/externalSubmit", [ "assignmentId" => $_SESSION["assignmentId"] ]);
}

function log_save_response($arr)
{
	logging("log_save_responses called");

	$log = fopen("./log.txt", "a");
        fwrite($log, json_encode($arr) . "\n\n");
        fclose($log);
}

function subject_save_response($arr)
{
	logging("subject_save_response called");

	$filename = "./data/" . $arr["worker_id"] . "_output.json";

        file_put_contents($filename, json_encode($arr));
}

function mysql_save_response($arr)
{
	logging("mysql_save_response called");

	$conn = dbConnect();

	$result = dbQuery($conn, "INSERT INTO responses SET bonus_paid=FALSE, start_time=:start_time, end_time=:end_time, phase_order=:phase_order, age=:age, gender=:gender, tries=:tries, during=:during, points_phase0=:points_phase0, points_phase1=:points_phase1, worker_id=:worker_id, assignment_id=:assignment_id, bonus=:bonus", [
			"start_time" => $arr["start_time"],
			"end_time" => $arr["end_time"],
			"phase_order" => $_SESSION["phase_order"],
                	"age" => $arr["age"],
                	"gender" => $arr["gender"],
                	"tries" => $arr["tries"],
                	"during" => $arr["during"],
			"points_phase0" => $arr["points_phase0"],
			"points_phase1" => $arr["points_phase1"],
			"worker_id" => $arr["worker_id"],
			"assignment_id" => $arr["assignment_id"],
			"bonus" => $arr["bonus"]
	]);

	$id = $conn->lastInsertId();

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "bar-choose")
		{
			foreach($trial["responses"] as $bar_response)
			{
				dbQuery($conn, "INSERT INTO bar_responses SET response=:value, offby=:offby, category=:category, category_index=:category_index, RID=$id, phase=" . $trial["phase"] . ", number=" . $trial["number"], $bar_response);
			}
		}
		else if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
//			var_dump($trial);
			dbQuery($conn, "INSERT INTO test_responses SET response=:result, points=:points, phase=:phase, sequence=:sequence, place=:place, RID=$id, next_num=:next_num", [
					"result" => $trial["result"],
					"points" => $trial["points"],
					"phase" => $trial["phase"],
					"sequence" => $trial["sequence"],
					"place" => $trial["place"],
					"next_num" => $trial["next_num"]
			]);

			for($i = 0; $i < count($trial["times"]); $i++)
			{
				dbQuery($conn, "INSERT INTO testing_times SET sequence=:sequence, RID=$id, place=:place, phase=:phase, time=:time", [
					"sequence" => $trial["sequence"],
					"place" => $i,
					"phase" => $trial["phase"],
					"time" => $trial["times"][$i]
				]);
			}
		}
		else if($trial["trial_type"] == "training_avg")
		{
			dbQuery($conn, "INSERT INTO training_responses SET sequence=:sequence, RID=$id, avg=:avg, response=:response, phase=:phase", [
					"sequence" => $trial["sequence"],
					"avg" => $trial["avg"],
					"response" => $trial["response"],
					"phase" => $trial["phase"]
			]);
		}
		else if($trial["trial_type"] == "store_order")
		{
			for($i = 0; $i < count($trial["order"]); $i++)
			{
				dbQuery($conn, "INSERT INTO testing_orders SET phase=:phase, sequence=:sequence, order_index=:order, RID=$id", [
					"phase" => $trial["phase"],
					"sequence" => $i,
					"order" => $trial["order"][$i]
				]);
			}
		}
	}
}

function dbConnect() {
    $dsn = "mysql:host=localhost;dbname=tickets_responses;charset=utf8";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ERRMODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $conn = new PDO($dsn, "tickets_user", "hats6789", $opts);
    return $conn;
}
function dbQuery($conn, $query, $values = array()) {
    if (isset($values)) {
        $stmt = $conn->prepare($query);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    else {
        $stmt = $conn->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}


function startSession() {

logging("startSession called");

$_SESSION = array();

$_SESSION["points"] = [];
$_SESSION["points"][0] = 0;
$_SESSION["points"][1] = 0;
$_SESSION["checked"] = [];
$_SESSION["checked"][0] = $_SESSION["checked"][1] = [];
$_SESSION["got_data"] = 0;
$_SESSION["finished"] = 0;
$_SESSION["checked_assoc"] = [];
$_SESSION["checked_assoc"][0] = $_SESSION["checked_assoc"][1] = [];

$_SESSION["start_time"] = get_time();


$_SESSION["training_data"] = [[
[196, 169, 184, 177, 166, 182, 182, 186, 181, 178],
[183, 176, 177, 190, 169, 182, 177, 187, 162, 177],
[175, 171, 168, 174, 186, 161, 180, 181, 196, 167],
[189, 182, 186, 190, 176, 175, 168, 158, 190, 165],
[181, 190, 165, 185, 179, 177, 181, 164, 177, 187]
],[
[214, 179, 215, 206, 108, 129, 121, 102, 188, 194],
[184, 235, 246, 195, 211, 266, 118, 110, 239, 174],
[229, 156, 218, 165, 189, 207, 59, 109, 202, 240],
[281, 230, 266, 170, 132, 228, 129, 142, 182, 140],
[127, 217, 232, 215, 170, 208, 284, 199, 154, 203]
]	];


/*
$_SESSION["training_data"] = [[
[168, 210, 158, 176, 182, 192, 174, 186, 178, 181],
[185, 237, 191, 177, 201, 154, 181, 196, 162, 195],
[182, 161, 190, 159, 197, 229, 184, 197, 174, 208],
[183, 154, 186, 177, 193, 185, 221, 174, 168, 132],
[185, 168, 194, 203, 198, 140, 172, 168, 188, 199]
],[
	[188, 122, 141, 221, 147, 173, 211, 246, 161, 205],
	[216, 200, 181, 160, 142, 189, 163, 191, 219, 218],
	[142, 185, 171, 189, 201, 230, 183, 123, 181, 152],
	[215, 176, 153, 181, 196, 133, 184, 179, 185, 201],
	[171, 187, 226, 123, 147, 172, 168, 172, 181, 191]
	]];
*/
$_SESSION["training_answers"] = [
// First training phase
[0, 0, 4, 6, 7, 3, 0], //[3, 7, 20, 10, 7, 2, 1]
// Second training phase
[1, 2, 4, 6, 6, 1, 0] // [4, 4, 6, 12, 13, 7, 4]
];

$_SESSION["training_categories"] = [
["$140 - $250","$151 - $160","$161 - $170", "$171 - $180", "$181 - $190", "$191 - $200", "$201 - $210"],
["$30 - $75", "$76 - $120", "$121 - $165", "$166 - $210", "$211 - $255", "$256 - $300", "$301 - $345"]
];


$_SESSION["testing_data"] = [[
[175, 179, 169, 193, 178, 182, 177, 172, 164, 183],
[175, 179, 169, 193, 178, 182, 177, 172, 164, 183],
[174, 179, 169, 194, 178, 183, 177, 173, 165, 184],
[174, 179, 169, 194, 178, 183, 177, 173, 165, 184],
[175, 179, 177, 193, 178, 182, 169, 172, 164, 183],
[175, 179, 177, 193, 178, 182, 169, 172, 164, 183],
[174, 179, 177, 194, 178, 183, 169, 173, 165, 184],
[174, 179, 177, 194, 178, 183, 169, 173, 165, 184],
[176, 180, 170, 194, 179, 183, 178, 173, 165, 184],
[176, 180, 170, 194, 179, 183, 178, 173, 165, 184],
[175, 180, 170, 195, 179, 184, 178, 174, 166, 185],
[175, 180, 170, 195, 179, 184, 178, 174, 166, 185],
[176, 180, 178, 194, 179, 183, 170, 173, 165, 184],
[176, 180, 178, 194, 179, 183, 170, 173, 165, 184],
[175, 180, 178, 195, 179, 184, 170, 174, 166, 185],
[175, 180, 178, 195, 179, 184, 170, 174, 166, 185],
[174, 178, 168, 192, 177, 181, 176, 171, 163, 182],
[174, 178, 168, 192, 177, 181, 176, 171, 163, 182],
[173, 178, 168, 193, 177, 182, 176, 172, 164, 183],
[173, 178, 168, 193, 177, 182, 176, 172, 164, 183],
[174, 178, 176, 192, 177, 181, 168, 171, 163, 182],
[174, 178, 176, 192, 177, 181, 168, 171, 163, 182],
[173, 178, 176, 193, 177, 182, 168, 172, 164, 183],
[173, 178, 176, 193, 177, 182, 168, 172, 164, 183],
[161, 180, 170, 174, 178, 198, 173, 182, 188, 176],
[163, 185, 165, 174, 181, 198, 173, 186, 187, 180],
[165, 187, 173, 177, 186, 196, 176, 191, 192, 181],
[168, 188, 171, 181, 184, 196, 180, 194, 195, 183],
[159, 178, 170, 174, 176, 194, 173, 182, 185, 175],
[166, 190, 170, 173, 178, 203, 172, 192, 198, 174],
[160, 185, 170, 174, 182, 196, 173, 186, 194, 177],
[157, 184, 163, 174, 181, 190, 168, 187, 189, 177],
[171, 180, 172, 174, 179, 188, 173, 183, 186, 175],
[170, 183, 172, 175, 180, 189, 174, 187, 188, 179],
[181, 180, 191, 186, 165, 197, 159, 167, 194, 185],
[188, 179, 154, 183, 173, 170, 177, 160, 169, 175],
[189, 185, 169, 168, 167, 171, 180, 173, 174, 188],
[181, 175, 182, 168, 183, 173, 153, 170, 193, 179],
[181, 179, 199, 188, 170, 172, 178, 187, 191, 177],
[195, 177, 197, 179, 171, 173, 175, 156, 163, 204],
[179, 174, 199, 178, 196, 167, 170, 184, 177, 190],
[180, 165, 177, 193, 186, 196, 187, 169, 171, 182],
[175, 193, 175, 160, 180, 170, 175, 205, 177, 167],
[188, 191, 179, 157, 181, 164, 166, 156, 175, 182],
[175, 150, 164, 155, 171, 199, 158, 180, 184, 196],
[179, 174, 172, 162, 202, 184, 165, 197, 185, 169],
[179, 186, 197, 201, 166, 187, 188, 193, 190, 177],
[180, 175, 173, 177, 191, 190, 184, 196, 174, 181],
[193, 173, 175, 182, 179, 198, 181, 171, 176, 187],
[175, 189, 193, 170, 179, 165, 180, 186, 177, 184]
],[
	[156, 175, 126, 246, 170, 190, 165, 140, 100, 196],
	[156, 175, 125, 245, 170, 190, 165, 140, 101, 196],
	[151, 176, 126, 251, 170, 196, 165, 145, 105, 201],
	[150, 176, 126, 251, 171, 195, 165, 145, 105, 200],
	[155, 175, 166, 245, 171, 190, 125, 140, 101, 196],
	[156, 176, 165, 246, 171, 190, 126, 141, 101, 196],
	[150, 175, 166, 251, 170, 196, 126, 145, 105, 201],
	[151, 175, 166, 251, 170, 195, 126, 146, 105, 201],
	[161, 180, 131, 251, 175, 196, 171, 146, 105, 201],
	[160, 180, 130, 251, 176, 195, 170, 146, 105, 200],
	[156, 181, 131, 256, 176, 200, 171, 151, 111, 206],
	[156, 181, 130, 255, 175, 200, 170, 150, 111, 205],
	[161, 180, 171, 250, 176, 195, 131, 146, 105, 201],
	[160, 180, 170, 251, 176, 195, 131, 146, 105, 200],
	[155, 181, 170, 255, 176, 200, 130, 150, 110, 205],
	[156, 181, 171, 256, 175, 200, 131, 150, 110, 206],
	[150, 171, 121, 241, 165, 185, 160, 136, 95, 191],
	[150, 171, 120, 241, 165, 185, 160, 135, 96, 190],
	[146, 171, 120, 245, 165, 191, 160, 140, 100, 196],
	[146, 170, 121, 245, 166, 190, 161, 141, 100, 195],
	[150, 170, 160, 240, 166, 185, 121, 136, 95, 191],
	[150, 170, 160, 241, 166, 186, 120, 136, 96, 191],
	[145, 171, 161, 245, 165, 191, 120, 140, 100, 196],
	[145, 170, 160, 245, 165, 190, 120, 141, 100, 195],
	[86, 180, 130, 151, 170, 271, 145, 191, 220, 160],
	[95, 205, 106, 151, 186, 271, 145, 210, 215, 181],
	[105, 216, 146, 165, 210, 260, 161, 235, 241, 185],
	[120, 221, 135, 185, 200, 261, 181, 251, 255, 196],
	[75, 171, 131, 151, 161, 250, 145, 190, 206, 156],
	[111, 231, 131, 146, 171, 295, 141, 241, 271, 150],
	[81, 205, 130, 151, 191, 261, 145, 210, 250, 165],
	[66, 201, 96, 150, 185, 230, 121, 216, 225, 165],
	[135, 180, 141, 151, 175, 220, 145, 196, 211, 155],
	[130, 195, 141, 155, 181, 226, 150, 216, 221, 175],
	[185, 180, 235, 210, 106, 266, 76, 115, 250, 205],
	[221, 175, 51, 196, 146, 130, 166, 80, 126, 156],
	[226, 205, 125, 120, 115, 135, 180, 145, 150, 220],
	[186, 156, 191, 120, 195, 145, 45, 130, 246, 176],
	[185, 175, 276, 221, 130, 141, 171, 216, 235, 165],
	[255, 166, 266, 175, 136, 145, 155, 61, 96, 301],
	[176, 150, 276, 171, 261, 116, 130, 201, 166, 231],
	[180, 106, 166, 245, 211, 261, 215, 125, 135, 190],
	[155, 245, 155, 81, 180, 130, 156, 306, 166, 115],
	[220, 236, 176, 65, 185, 101, 110, 61, 156, 191],
	[155, 30, 101, 55, 136, 276, 70, 181, 200, 261],
	[176, 150, 141, 91, 290, 201, 106, 266, 206, 126],
	[175, 211, 266, 286, 111, 216, 220, 245, 230, 165],
	[181, 156, 145, 165, 236, 231, 201, 260, 150, 185],
	[246, 145, 155, 191, 176, 270, 185, 136, 160, 215],
	[155, 225, 245, 130, 176, 106, 180, 211, 165, 200]
	]];

	/*
$_SESSION["testing_data"] = [[
[176, 186, 163, 214, 179, 187, 176, 166, 153, 208], // 0
[175, 187, 163, 209, 180, 188, 176, 165, 154, 208], // 1
[174, 186, 163, 212, 181, 187, 176, 166, 153, 209],
[175, 186, 163, 211, 179, 187, 176, 165, 153, 210],
[176, 186, 176, 210, 179, 187, 163, 166, 153, 208],
[175, 187, 176, 209, 180, 188, 163, 165, 154, 208],
[174, 186, 176, 217, 181, 187, 163, 166, 153, 209],
[175, 186, 176, 224, 179, 187, 163, 165, 153, 210],
[174, 186, 167, 211, 181, 188, 178, 168, 208, 156],
[175, 185, 167, 224, 181, 186, 178, 168, 208, 156],
[175, 186, 167, 225, 179, 187, 178, 169, 209, 153], // 10
[175, 187, 167, 212, 179, 187, 178, 169, 208, 153],
[174, 186, 178, 217, 181, 188, 167, 168, 208, 156],
[175, 185, 178, 215, 181, 186, 167, 168, 208, 156],
[175, 186, 178, 212, 179, 187, 167, 169, 209, 153],
[175, 187, 178, 209, 179, 187, 167, 169, 208, 153],
[175, 186, 158, 211, 180, 187, 177, 166, 209, 153],
[175, 186, 158, 209, 181, 188, 177, 167, 208, 154],
[176, 187, 158, 210, 181, 187, 177, 165, 208, 153],
[176, 185, 158, 217, 180, 186, 177, 166, 209, 154],
[175, 186, 177, 221, 180, 187, 158, 166, 209, 153], // 20
[175, 186, 177, 209, 181, 188, 158, 167, 208, 154],
[176, 187, 177, 225, 181, 187, 158, 165, 208, 153],
[176, 185, 177, 218, 180, 186, 158, 166, 209, 154],
[161, 193, 175, 181, 185, 224, 180, 199, 218, 184],
[156, 201, 162, 166, 185, 214, 165, 202, 204, 179],
[152, 200, 158, 173, 182, 207, 161, 202, 204, 180],
[168, 192, 171, 178, 188, 217, 172, 195, 197, 186],
[169, 196, 170, 184, 187, 211, 171, 201, 202, 185],
[150, 185, 154, 161, 173, 199, 155, 188, 191, 168],
[157, 191, 165, 175, 182, 203, 170, 195, 200, 176], // 30
[148, 171, 150, 163, 167, 220, 162, 186, 187, 165],
[161, 193, 167, 187, 190, 224, 174, 194, 195, 188],
[159, 183, 166, 168, 173, 205, 167, 187, 191, 170],
[181, 155, 148, 161, 168, 187, 172, 199, 176, 194],
[168, 154, 172, 185, 195, 144, 184, 211, 177, 227],
[188, 200, 165, 172, 198, 174, 177, 154, 177, 180],
[198, 193, 169, 163, 202, 182, 152, 164, 177, 171],
[168, 199, 196, 198, 185, 195, 227, 163, 177, 166],
[189, 167, 180, 197, 165, 168, 172, 213, 178, 171],
[189, 150, 175, 160, 188, 207, 158, 194, 178, 199], // 40
[201, 207, 155, 158, 156, 165, 167, 135, 178, 177],
[222, 173, 202, 197, 143, 205, 198, 183, 178, 186],
[201, 187, 157, 184, 198, 179, 188, 185, 178, 170],
[180, 159, 149, 175, 185, 141, 163, 174, 178, 207],
[212, 192, 188, 184, 167, 193, 189, 164, 176, 170],
[210, 184, 226, 193, 215, 158, 199, 175, 176, 208],
[200, 181, 203, 152, 175, 171, 160, 154, 176, 191],
[181, 215, 169, 147, 182, 193, 177, 184, 176, 179], // 48
[183, 163, 185, 180, 166, 148, 160, 186, 176, 184]
],[
[173, 191, 146, 242, 178, 194, 172, 152, 126, 235], // 0
[169, 193, 146, 237, 180, 196, 172, 150, 129, 236], // 1
[168, 191, 146, 252, 183, 193, 172, 153, 126, 238],
[170, 193, 146, 268, 178, 195, 172, 149, 126, 241],
[172, 191, 176, 272, 179, 194, 146, 152, 126, 235],
[171, 195, 172, 242, 179, 196, 146, 150, 128, 235],
[168, 191, 172, 240, 183, 193, 146, 151, 125, 238],
[171, 191, 172, 268, 179, 194, 146, 150, 127, 239],
[168, 192, 154, 237, 182, 196, 176, 156, 235, 133],
[169, 191, 154, 268, 183, 197, 176, 155, 236, 133],
[169, 193, 154, 255, 178, 194, 176, 158, 239, 126], // 10
[169, 194, 154, 271, 178, 201, 176, 158, 236, 127],
[168, 192, 176, 242, 183, 197, 154, 156, 237, 132],
[170, 190, 176, 251, 183, 192, 154, 157, 236, 131],
[169, 192, 176, 244, 178, 194, 154, 159, 237, 125],
[170, 193, 176, 245, 178, 194, 154, 159, 236, 126],
[171, 192, 136, 265, 180, 194, 174, 152, 239, 127],
[171, 192, 136, 243, 182, 195, 174, 154, 236, 128],
[173, 194, 136, 239, 182, 200, 174, 150, 236, 127],
[172, 190, 136, 270, 179, 192, 174, 152, 237, 129],
[169, 192, 174, 246, 180, 195, 136, 152, 238, 126], // 20
[170, 192, 174, 242, 181, 196, 136, 153, 236, 128],
[172, 194, 174, 243, 182, 193, 136, 150, 237, 126],
[173, 191, 174, 270, 180, 193, 136, 153, 239, 129],
[142, 207, 171, 182, 190, 269, 180, 218, 256, 188],
[132, 222, 144, 153, 190, 248, 151, 224, 228, 179],
[124, 220, 136, 165, 185, 234, 143, 225, 228, 179],
[157, 205, 161, 175, 197, 254, 165, 210, 214, 192],
[157, 213, 159, 187, 194, 242, 161, 222, 225, 189],
[121, 191, 128, 141, 166, 217, 129, 196, 202, 157],
[135, 202, 150, 169, 183, 225, 160, 211, 220, 172], // 30
[117, 161, 120, 147, 153, 259, 144, 191, 195, 149],
[143, 206, 153, 194, 201, 267, 168, 208, 210, 196],
[137, 187, 152, 156, 166, 231, 155, 194, 202, 160],
[181, 130, 116, 142, 157, 193, 165, 218, 173, 207],
[155, 129, 164, 189, 210, 108, 187, 241, 174, 275],
[196, 221, 149, 164, 216, 167, 175, 128, 176, 181],
[216, 206, 158, 145, 224, 185, 124, 149, 175, 162], // 37
[156, 218, 212, 216, 191, 210, 275, 146, 174, 152],
[197, 153, 181, 215, 150, 155, 164, 247, 177, 162],
[198, 119, 169, 141, 195, 233, 136, 208, 177, 217], // 40
[221, 234, 131, 136, 132, 149, 154, 90, 176, 173],
[263, 165, 224, 214, 105, 230, 215, 185, 177, 193],
[221, 193, 135, 188, 217, 179, 196, 190, 175, 159],
[181, 138, 118, 170, 190, 101, 146, 169, 175, 233],
[244, 203, 196, 189, 154, 206, 197, 149, 171, 159],
[240, 188, 273, 207, 250, 136, 217, 170, 171, 236],
[219, 183, 227, 124, 176, 161, 140, 129, 171, 201],
[182, 249, 159, 114, 184, 205, 175, 189, 172, 178], // 48
[186, 146, 191, 179, 152, 116, 140, 201, 172, 187]
]];
*/


$_SESSION["training_avg_ranges"] = [[170, 190], [130, 230]];

// Random test sequence order
$_SESSION["testing_data_order"] = [];

// loop through phases
for($i = 0; $i < count($_SESSION["testing_data"]); $i++)
{
	$_SESSION["testing_data_order"][$i] = [];

	for($j = 0; $j < count($_SESSION["testing_data"][$i]); $j++)
		$_SESSION["testing_data_order"][$i][$j] = $j;

	// shuffle sequence order
	shuffle($_SESSION["testing_data_order"][$i]);

	// shuffle sequences (apply sequence order)
	$temp = $_SESSION["testing_data"][$i];

	// loop through all sequences in this phase
	for($j = 0; $j < count($_SESSION["testing_data"][$i]); $j++)
		$_SESSION["testing_data"][$i][$j] = $temp[$_SESSION["testing_data_order"][$i][$j]];
}

$v = mt_rand(1, 2);

$_SESSION["phase_order"] = $v - 1;

if($v === 2)
{
	$temp = $_SESSION["training_data"][0];
	$_SESSION["training_data"][0] = $_SESSION["training_data"][1];
	$_SESSION["training_data"][1] = $temp;

	$temp = $_SESSION["training_answers"][0];
        $_SESSION["training_answers"][0] = $_SESSION["training_answers"][1];
        $_SESSION["training_answers"][1] = $temp;

	$temp = $_SESSION["testing_data"][0];
        $_SESSION["testing_data"][0] = $_SESSION["testing_data"][1];
        $_SESSION["testing_data"][1] = $temp;

	$temp = $_SESSION["training_categories"][0];
        $_SESSION["training_categories"][0] = $_SESSION["training_categories"][1];
        $_SESSION["training_categories"][1] = $temp;

	$temp = $_SESSION["training_avg_ranges"][0];
        $_SESSION["training_avg_ranges"][0] = $_SESSION["training_avg_ranges"][1];
        $_SESSION["training_avg_ranges"][1] = $temp;
}

}

?>
