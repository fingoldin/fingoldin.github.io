<?php

if(!session_id())
	session_start();

if(isset($_POST["f7g12d"]) && isset($_SESSION["testing_data"]) && isset($_SESSION["got_data"]) && isset($_SESSION["training_data"]) && isset($_SESSION["training_answers"]) && $_SESSION["got_data"] == 0)
{
	$arr = [
		"testing" => $_SESSION["testing_data"],
		"training" => $_SESSION["training_data"],
		"answers" => $_SESSION["training_answers"]
	];

	echo json_encode($arr);
	$_SESSION["got_data"] = 1;
}

?>
