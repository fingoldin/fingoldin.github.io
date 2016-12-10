<?php

if(!session_id())
	session_start();

if(isset($_SESSION["start_time"]) && isset($_SESSION["finished"]) && $_SESSION["finished"] == 0 && isset($_POST["data"]) && isset($_SESSION["points"]))
{
	$log = fopen("../submitted.txt", "a");
	if(!$log) {
		echo "server error";
		exit;
	}

	date_default_timezone_set("America/New_York");
        $time = date("m/d/Y h:i:s a");

	$arr = ["start_time" => $_SESSION["start_time"], "end_time" => $time, "total_points" => $_SESSION["points"], "data" => $_POST["data"]];

	$str = json_encode($arr);;

	fwrite($log, "start_time: " . $_SESSION["start_time"] . "  end_time: " . $time . "  total points: " . $_SESSION["points"] . "\ndata:\n" . $str . "\n\n\n");

	fclose($log);

	echo "ser: " . $str;

	$_SESSION["finished"] = 1;

       	$_SESSION = array();
       	session_destroy();
}

?>
