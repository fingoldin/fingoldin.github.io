<?php

require("./repos/mturk-php/mturk.php");

function store_url()
{
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
	$percent = round(100 * ($points / 300));
	$b = intval(round(4 * $percent));

	if($b > 400)
		return 400;
	else if($b < 0)
		return 0;
	else
		return $b;
}

function grant_bonus($b, $worker_id, $assignment_id)
{
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
	$log = fopen("./log.txt", "a");
        fwrite($log, json_encode($arr) . "\n\n");
        fclose($log);
}

function subject_save_response($arr)
{
	$filename = "./data/" . $arr["worker_id"] . "_output.json";

        file_put_contents($filename, json_encode($arr));
}

function mysql_save_response($arr)
{
	$conn = dbConnect();

	$result = dbQuery($conn, "INSERT INTO responses SET start_time=:start_time, end_time=:end_time, phase_order=:phase_order, age=:age, gender=:gender, tries=:tries, during=:during, points_phase0=:points_phase0, points_phase1=:points_phase1, worker_id=:worker_id, assignment_id=:assignment_id, bonus=:bonus", [
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
[168, 210, 158, 176, 182, 192, 174, 186, 178, 181],
[185, 237, 191, 177, 201, 154, 181, 196, 162, 195],
[182, 161, 190, 159, 197, 229, 184, 197, 174, 208],
[183, 154, 186, 177, 193, 185, 221, 174, 168, 132],
[185, 168, 194, 203, 198, 140, 172, 168, 188, 199]
], [
[190, 105, 220, 151, 111, 213, 261, 183, 211, 191],
[146, 127, 262, 226, 189, 128, 250, 228, 242, 188],
[156, 193, 233, 205, 154, 235, 208, 200, 182, 219],
[236, 184, 224, 280, 227, 143, 233, 210, 192, 170],
[164, 233, 158, 213, 220, 274, 177, 202, 170, 209]
]];

$_SESSION["training_answers"] = [
// First training phase
[0, 1, 3, 6, 6, 3, 1], //[3, 7, 20, 10, 7, 2, 1]
// Second training phase
[1, 2, 2, 5, 5, 3, 2] // [4, 4, 6, 12, 13, 7, 4]
];

$_SESSION["training_categories"] = [
["$106 - $134","$135 - $150", "$151 - $165", "$166 - $180", "$181 - $195", "$196 - $210", "$211 - $225"],
["$105 - $130", "$131 - $155", "$156 - $180", "$181 - $205", "$206 - $230", "$231 - $255", "$256 - $280"]
];


$_SESSION["testing_data"] = [[
[178, 188, 158, 217, 183, 191, 161, 177, 200, 146],
[169, 180, 162, 216, 174, 182, 165, 166, 205, 154],
[182, 196, 175, 215, 186, 201, 180, 181, 205, 153],
[180, 193, 154, 230, 189, 199, 155, 178, 207, 149],
[179, 198, 170, 220, 190, 201, 171, 175, 202, 140],
[175, 185, 150, 214, 180, 188, 162, 170, 197, 143],
[173, 183, 165, 217, 182, 201, 170, 171, 206, 155],
[183, 198, 171, 217, 188, 203, 172, 173, 207, 155],
[178, 191, 167, 228, 187, 197, 174, 176, 205, 147],
[177, 196, 166, 218, 188, 199, 167, 173, 200, 138],
[164, 183, 161, 210, 165, 186, 158, 160, 142, 203],
[175, 188, 174, 210, 177, 191, 167, 170, 149, 207],
[181, 196, 172, 207, 191, 200, 170, 171, 148, 202],
[170, 196, 163, 210, 191, 200, 154, 155, 150, 201],
[184, 188, 180, 212, 186, 200, 171, 175, 163, 208],
[178, 186, 170, 199, 180, 187, 166, 168, 152, 194],
[169, 182, 167, 226, 178, 185, 162, 163, 145, 190],
[169, 187, 168, 209, 171, 191, 150, 165, 149, 200],
[186, 193, 183, 213, 190, 194, 165, 178, 138, 209],
[188, 198, 184, 226, 192, 208, 175, 181, 158, 217],
[161, 193, 175, 181, 185, 224, 180, 199, 218, 184],
[156, 201, 162, 166, 185, 214, 165, 202, 204, 179],
[152, 200, 158, 173, 182, 207, 161, 202, 204, 180],
[168, 192, 171, 178, 188, 217, 172, 195, 197, 186],
[169, 196, 170, 184, 187, 211, 171, 201, 202, 185],
[150, 185, 154, 161, 173, 199, 155, 188, 191, 168],
[157, 191, 165, 175, 182, 203, 170, 195, 200, 176],
[148, 171, 150, 163, 167, 220, 162, 186, 187, 165],
[161, 193, 167, 187, 190, 224, 174, 194, 195, 188],
[159, 183, 166, 168, 173, 205, 167, 187, 191, 170],
[203, 196, 174, 194, 182, 138, 152, 210, 150, 188],
[162, 183, 196, 179, 210, 160, 184, 149, 213, 182],
[182, 201, 146, 196, 205, 207, 202, 175, 170, 200],
[197, 155, 181, 187, 206, 162, 173, 208, 174, 169],
[181, 155, 148, 161, 168, 187, 172, 199, 174, 194],
[168, 154, 172, 185, 195, 144, 184, 211, 204, 227],
[188, 200, 165, 172, 198, 174, 177, 154, 190, 180],
[198, 193, 169, 163, 202, 182, 152, 164, 191, 171],
[168, 199, 196, 198, 185, 195, 227, 163, 191, 166],
[189, 167, 180, 197, 165, 168, 172, 213, 182, 171],
[189, 150, 175, 160, 188, 207, 158, 194, 195, 199],
[201, 207, 155, 158, 156, 165, 167, 135, 225, 177],
[222, 173, 202, 197, 143, 205, 198, 183, 158, 186],
[201, 187, 157, 184, 198, 179, 188, 185, 164, 170],
[180, 159, 149, 175, 185, 141, 163, 174, 202, 207],
[212, 192, 188, 184, 167, 193, 189, 164, 181, 170],
[210, 184, 226, 193, 215, 158, 199, 175, 164, 208],
[200, 181, 203, 152, 175, 171, 160, 154, 196, 191],
[181, 215, 169, 147, 182, 193, 177, 184, 159, 179],
[183, 163, 185, 180, 166, 148, 160, 186, 182, 184]
], [
	[196, 217, 157, 275, 206, 222, 163, 195, 240, 156],
	[179, 200, 165, 273, 188, 205, 171, 172, 251, 148],
	[205, 233, 191, 271, 213, 243, 201, 202, 250, 147],
	[200, 226, 148, 301, 219, 238, 151, 197, 254, 138],
	[198, 237, 180, 280, 220, 242, 182, 191, 245, 142],
	[190, 210, 140, 269, 200, 216, 165, 181, 235, 126],
	[186, 206, 170, 274, 204, 243, 180, 182, 253, 150],
	[207, 237, 182, 274, 217, 246, 184, 187, 255, 150],
	[197, 222, 175, 296, 214, 235, 189, 193, 251, 134],
	[194, 233, 173, 276, 216, 238, 175, 187, 240, 170],
	[169, 206, 162, 261, 170, 213, 157, 161, 147, 246],
	[190, 216, 189, 260, 194, 222, 175, 180, 139, 254],
	[203, 232, 184, 254, 223, 240, 180, 183, 136, 245],
	[181, 233, 167, 261, 222, 241, 148, 151, 141, 243],
	[209, 217, 201, 265, 213, 240, 182, 191, 166, 256],
	[196, 212, 181, 238, 201, 215, 173, 176, 144, 229],
	[179, 205, 174, 292, 197, 211, 165, 166, 130, 221],
	[179, 214, 177, 259, 182, 223, 140, 170, 139, 241],
	[213, 227, 207, 266, 220, 228, 170, 197, 117, 259],
	[217, 236, 209, 292, 224, 256, 191, 203, 156, 275],
	[162, 227, 191, 202, 211, 288, 201, 238, 277, 208],
	[153, 242, 165, 172, 210, 268, 171, 244, 248, 198],
	[145, 240, 157, 186, 204, 255, 163, 244, 249, 200],
	[176, 224, 182, 196, 217, 274, 184, 231, 235, 212],
	[178, 233, 180, 208, 215, 263, 182, 243, 244, 210],
	[141, 211, 148, 163, 187, 238, 151, 217, 223, 177],
	[154, 222, 170, 190, 204, 246, 180, 230, 240, 193],
	[137, 182, 140, 167, 174, 281, 165, 212, 214, 170],
	[163, 227, 175, 215, 220, 289, 189, 229, 230, 217],
	[158, 206, 173, 176, 186, 251, 175, 215, 222, 180],
	[247, 232, 188, 229, 205, 116, 145, 260, 140, 217],
	[165, 206, 233, 198, 261, 161, 209, 138, 266, 205],
	[205, 242, 133, 233, 250, 255, 245, 190, 181, 240],
	[235, 151, 203, 215, 253, 164, 187, 256, 188, 178],
	[202, 150, 137, 163, 176, 215, 185, 238, 188, 228],
	[177, 149, 184, 211, 230, 129, 209, 262, 248, 295],
	[216, 241, 171, 184, 237, 188, 195, 148, 221, 200],
	[236, 226, 178, 167, 245, 204, 145, 168, 223, 183],
	[177, 239, 233, 236, 210, 231, 294, 167, 223, 173],
	[218, 175, 200, 235, 171, 177, 184, 267, 205, 183],
	[218, 140, 191, 160, 216, 255, 156, 229, 231, 239],
	[242, 255, 151, 156, 153, 171, 174, 110, 291, 194],
	[284, 187, 244, 234, 126, 251, 236, 206, 156, 213],
	[242, 214, 155, 209, 236, 199, 216, 211, 169, 181],
	[201, 158, 138, 190, 211, 122, 167, 188, 244, 255],
	[265, 225, 216, 208, 175, 226, 218, 169, 203, 180],
	[261, 208, 293, 226, 271, 156, 239, 191, 169, 257],
	[240, 203, 246, 144, 191, 183, 161, 149, 233, 222],
	[202, 270, 179, 135, 205, 227, 195, 209, 159, 199],
	[206, 166, 211, 200, 172, 136, 161, 213, 205, 209]
	]];



$_SESSION["training_avg_ranges"] = [[160, 200], [170, 230]];

// Random test sequences
$_SESSION["testing_data_order"] = [];

for($i = 0; $i < count($_SESSION["testing_data"]); $i++)
{
	$_SESSION["testing_data_order"][$i] = [];

	for($j = 0; $j < count($_SESSION["testing_data"][$i]); $j++)
		$_SESSION["testing_data_order"][$i][$j] = $j;

	shuffle($_SESSION["testing_data_order"][$i]);

	$temp = $_SESSION["testing_data"][$i];
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
