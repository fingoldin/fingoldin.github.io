<?php

require("./includes.php");

if(!session_id())
	session_start();

if(isset($_SESSION["start_time"]) && isset($_SESSION["finished"]) && $_SESSION["finished"] == 0 && isset($_POST["data"]) && isset($_SESSION["points"]))
{
	$time = get_time();

	$arr = ["start_time" => $_SESSION["start_time"], "end_time" => $time, "total_points" => $_SESSION["points"], "data" => json_decode($_POST["data"], true)];

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
	}

	submit_response($arr);

	$_SESSION["finished"] = 1;

       	$_SESSION = array();
       	session_destroy();
}

?>
