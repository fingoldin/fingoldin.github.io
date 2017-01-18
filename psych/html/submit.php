<?php

if(!session_id())
        session_start();

require("./includes.php");

if(isset($_SESSION["start_time"]) && isset($_SESSION["finished"]) && $_SESSION["finished"] == 0 && isset($_POST["data"]) && isset($_SESSION["points"]) && isset($_SESSION["phase_order"]) && isset($_POST["subject_id"]))
{
	$time = get_time();

	$arr = [
		"start_time" => $_SESSION["start_time"],
		"end_time" => $time,
		"points_phase0" => $_SESSION["points"][0],
		"points_phase1" => $_SESSION["points"][1],
		"phase_order" => $_SESSION["phase_order"],
		"age" => 0,
		"gender" => "m",
		"tries" => 1,
		"during" => "Nothing",
		"subject_id" => $_POST["subject_id"],
		"data" => json_decode($_POST["data"], true)
	];

	foreach($arr["data"] as $trial)
	{
		if($trial["trial_type"] == "ticket-choose" && $trial["sequence"] > -1)
		{
//			var_dump($trial);
			$arr2 = $_SESSION["testing_data"][$trial["phase"]][$trial["sequence"]];
			sort($arr2);

			//echo "tp: " . $trial["points"] . " arr: " . $_SESSION["checked_assoc"][$trial["phase"]][$trial["sequence"]];
			if($trial["points"] !== $_SESSION["checked_assoc"][$trial["phase"]][$trial["sequence"]] ||
			   $trial["place"] !== array_search($trial["result"], $arr2))
			{
				echo "Looks like we've got ourselves a script kiddie here.";
				exit;
			}
		}
		else if($trial["trial_type"] == "age")
		{
			$arr["age"] = $trial["age"];
			$arr["gender"] = $trial["gender"];
		}
		else if($trial["trial_type"] == "instructions_check")
			$arr["tries"] = $trial["tries"];
		else if($trial["trial_type"] == "final")
			$arr["during"] = $trial["during"];
	}

	mysql_save_response($arr);
	log_save_response($arr);
	subject_save_response($arr);

	$_SESSION["finished"] = 1;

       	$_SESSION = array();
       	session_destroy();
}

?>
