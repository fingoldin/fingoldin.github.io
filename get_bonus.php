<?php 

if(!session_id())
	session_start();

require("./includes.php");

if(isset($_POST["points"]))
	echo json_encode(["bonus" => get_bonus($_POST["points"])]);

?>
