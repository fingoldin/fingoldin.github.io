<?php

if(!session_id())
	session_start();

if(isset($_POST["f7g12d"]) && isset($_SESSION["testing_data"]) && isset($_SESSION["got_data"]) && $_SESSION["got_data"] == 0)
{
	echo json_encode($_SESSION["testing_data"]);
	$_SESSION["got_data"] = 1;
}

?>
