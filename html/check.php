<?php

if(!session_id())
	session_start();

//require("../data.php");

function checkAnswer($phase, $sequence, $answer)
{
	if(!isset($_SESSION["points"]) || !isset($_SESSION["checked"]) || !isset($_SESSION["testing_data"]))
		echo "error";
	else if(!in_array($sequence, $_SESSION["checked"][$phase]))
	{
		$data = [
			"points" => 0
		//	"place" => 1
		];

		$arr = $_SESSION["testing_data"][$phase][$sequence];
		sort($arr);

		$a = intval($answer);

		$p = 0;

		if($arr[0] == $a)
			$p = 2;
		else if($arr[1] == $a)
			$p = 1;

		$data["points"] = $_SESSION["points"] + $p;
		if($data["points"] > 200)
		{
			$data["points"] = 200;
			$_SESSION["points"] = 200;
		}
		else
			$_SESSION["points"] += $p;
		//$data["place"] = array_search($a, $arr) + 1;

		array_push($_SESSION["checked"][$phase], $sequence);

		echo json_encode($data);
	}
}

if(isset($_POST["phase"]) && isset($_POST["sequence"]) && isset($_POST["answer"]))
	checkAnswer($_POST["phase"], $_POST["sequence"], $_POST["answer"]);

?>
