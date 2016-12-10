<!DOCTYPE html>

<?php

if(session_id())
{
	$_SESSION = array();
	session_destroy();
}

session_start();

$_SESSION["points"] = 0;
$_SESSION["checked"] = [];
$_SESSION["checked"][0] = $_SESSION["checked"][1] = [];
$_SESSION["got_data"] = 0;

$_SESSION["testing_data"] = [[
[191, 168, 200, 169, 149, 209, 187, 171, 165, 150],
[183, 213, 138, 190, 186, 209, 178, 194, 165, 193],
[192, 158, 184, 208, 188, 226, 181, 198, 169, 217],
[184, 180, 224, 165, 181, 199, 185, 193, 218, 126],
[165, 163, 166, 201, 185, 179, 156, 204, 202, 214],
[154, 140, 157, 186, 167, 192, 188, 197, 195, 217],
[176, 143, 179, 185, 184, 196, 187, 202, 201, 211],
[154, 145, 155, 168, 161, 185, 173, 191, 188, 199],
[162, 157, 170, 176, 175, 191, 182, 200, 195, 203],
[151, 148, 157, 165, 163, 171, 167, 187, 186, 220],
[195, 194, 190, 224, 188, 193, 164, 161, 173, 187],
[191, 187, 173, 205, 170, 183, 164, 159, 165, 168],
[203, 196, 188, 210, 182, 194, 138, 128, 152, 174],
[210, 196, 183, 213, 182, 184, 160, 149, 162, 179],
[205, 202, 200, 207, 196, 201, 146, 137, 175, 182],
[144, 205, 165, 201, 171, 213, 174, 206, 184, 191],
[155, 197, 162, 187, 169, 208, 173, 206, 174, 181],
[148, 187, 155, 181, 161, 199, 168, 194, 172, 174],
[144, 204, 154, 195, 168, 227, 172, 211, 184, 185],
[154, 190, 165, 188, 172, 200, 174, 198, 177, 180],
[163, 185, 168, 191, 196, 166, 199, 195, 227, 198],
[185, 163, 168, 191, 196, 166, 199, 195, 227, 198],
[185, 168, 163, 191, 196, 166, 199, 195, 227, 198],
[185, 168, 191, 163, 196, 166, 199, 195, 227, 198],
[185, 168, 191, 196, 163, 166, 199, 195, 227, 198],
[191, 168, 200, 169, 149, 209, 187, 171, 165, 150],
[183, 213, 138, 190, 186, 209, 178, 194, 165, 193],
[192, 158, 184, 208, 188, 226, 181, 198, 169, 217],
[184, 180, 224, 165, 181, 199, 185, 193, 218, 126],
[165, 163, 166, 201, 185, 179, 156, 204, 202, 214],
[154, 140, 157, 186, 167, 192, 188, 197, 195, 217],
[176, 143, 179, 185, 184, 196, 187, 202, 201, 211],
[154, 145, 155, 168, 161, 185, 173, 191, 188, 199],
[162, 157, 170, 176, 175, 191, 182, 200, 195, 203],
[151, 148, 157, 165, 163, 171, 167, 187, 186, 220],
[195, 194, 190, 224, 188, 193, 164, 161, 173, 187],
[191, 187, 173, 205, 170, 183, 164, 159, 165, 168],
[203, 196, 188, 210, 182, 194, 138, 128, 152, 174],
[210, 196, 183, 213, 182, 184, 160, 149, 162, 179],
[205, 202, 200, 207, 196, 201, 146, 137, 175, 182],
[144, 205, 165, 201, 171, 213, 174, 206, 184, 191],
[155, 197, 162, 187, 169, 208, 173, 206, 174, 181],
[148, 187, 155, 181, 161, 199, 168, 194, 172, 174],
[144, 204, 154, 195, 168, 227, 172, 211, 184, 185],
[154, 190, 165, 188, 172, 200, 174, 198, 177, 180],
[163, 185, 168, 191, 196, 166, 199, 195, 227, 198],
[185, 163, 168, 191, 196, 166, 199, 195, 227, 198],
[185, 168, 163, 191, 196, 166, 199, 195, 227, 198],
[185, 168, 191, 163, 196, 166, 199, 195, 227, 198],
[185, 168, 191, 196, 163, 166, 199, 195, 227, 198]
], [
[219, 222, 305, 181, 165, 170, 188, 187, 195, 215],
[141, 225, 160, 141, 198, 266, 199, 228, 194, 187],
[187, 209, 264, 118, 159, 171, 162, 210, 177, 254],
[207, 196, 268, 185, 208, 135, 158, 108, 148, 192],
[229, 246, 217, 177, 185, 235, 191, 237, 179, 232],
[156, 140, 159, 216, 191, 228, 217, 239, 231, 254],
[151, 30, 153, 170, 155, 193, 175, 253, 242, 290],
[156, 125, 185, 212, 206, 235, 234, 250, 244, 284],
[169, 153, 180, 208, 197, 214, 211, 236, 216, 243],
[139, 123, 159, 188, 166, 199, 191, 244, 211, 254],
[225, 224, 216, 265, 207, 218, 169, 154, 174, 202],
[269, 260, 238, 292, 226, 255, 167, 150, 189, 208],
[232, 221, 189, 245, 182, 203, 144, 114, 147, 160],
[226, 207, 202, 270, 199, 204, 158, 134, 178, 194],
[232, 227, 213, 234, 207, 224, 180, 152, 182, 200],
[138, 234, 141, 218, 168, 271, 169, 248, 193, 198],
[117, 218, 158, 198, 161, 293, 165, 252, 181, 182],
[151, 242, 167, 231, 187, 254, 192, 245, 195, 208],
[107, 207, 123, 198, 135, 237, 157, 213, 168, 186],
[165, 223, 169, 219, 189, 267, 191, 264, 192, 195],
[115, 171, 154, 181, 226, 123, 245, 189, 251, 237],
[171, 115, 154, 181, 226, 123, 245, 189, 251, 237],
[171, 154, 115, 181, 226, 123, 245, 189, 251, 237],
[171, 154, 181, 226, 226, 123, 245, 189, 251, 237],
[171, 154, 181, 115, 115, 123, 245, 189, 251, 237],
[219, 222, 305, 181, 165, 170, 188, 187, 195, 215],
[141, 225, 160, 141, 198, 266, 199, 228, 194, 187],
[187, 209, 264, 118, 159, 171, 162, 210, 177, 254],
[207, 196, 268, 185, 208, 135, 158, 108, 148, 192],
[229, 246, 217, 177, 185, 235, 191, 237, 179, 232],
[156, 140, 159, 216, 191, 228, 217, 239, 231, 254],
[151, 30, 153, 170, 155, 193, 175, 253, 242, 290],
[156, 125, 185, 212, 206, 235, 234, 250, 244, 284],
[169, 153, 180, 208, 197, 214, 211, 236, 216, 243],
[139, 123, 159, 188, 166, 199, 191, 244, 211, 254],
[225, 224, 216, 265, 207, 218, 169, 154, 174, 202],
[269, 260, 238, 292, 226, 255, 167, 150, 189, 208],
[232, 221, 189, 245, 182, 203, 144, 114, 147, 160],
[226, 207, 202, 270, 199, 204, 158, 134, 178, 194],
[232, 227, 213, 234, 207, 224, 180, 152, 182, 200],
[138, 234, 141, 218, 168, 271, 169, 248, 193, 198],
[117, 218, 158, 198, 161, 293, 165, 252, 181, 182],
[151, 242, 167, 231, 187, 254, 192, 245, 195, 208],
[107, 207, 123, 198, 135, 237, 157, 213, 168, 186],
[165, 223, 169, 219, 189, 267, 191, 264, 192, 195],
[115, 171, 154, 181, 226, 123, 245, 189, 251, 237],
[171, 115, 154, 181, 226, 123, 245, 189, 251, 237],
[171, 154, 115, 181, 226, 123, 245, 189, 251, 237],
[171, 154, 181, 226, 226, 123, 245, 189, 251, 237],
[171, 154, 181, 115, 115, 123, 245, 189, 251, 237]
]];
?>

<head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="/jsPsych/jspsych.js"></script>
<script src="/jsPsych/plugins/jspsych-text.js"></script>
<script src="/jsPsych/plugins/jspsych-html.js"></script>
<script src="/jsPsych/plugins/jspsych-animation.js"></script>
<script src="/jsPsych/plugins/jspsych-number-animation.js"></script>
<script src="/jsPsych/plugins/jspsych-html-animation.js"></script>
<script src="/jsPsych/plugins/jspsych-bar-choose.js"></script>
<script src="/jsPsych/plugins/jspsych-ticket-choose.js"></script>
<script src="/jsPsych/plugins/jspsych-final.js"></script>
<script src="/utils/general.js"></script>
<script src="/utils/bar-choose-plugin.js"></script>
<script src="/utils/jquery.transform2d.js"></script>
<link href="/jsPsych/css/jspsych.css" rel="stylesheet" type="text/css"></link>
<link href="/utils/general.css" rel="stylesheet" type="text/css"></link>
<link href="/utils/bar-choose-plugin.css" rel="stylesheet" type="text/css"></link>
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet"> 
<link href="/utils/consent.css" rel="stylesheet" type="text/css"></link>
<link href="/utils/start.css" rel="stylesheet" type="text/css"></link>
<link href="/utils/points.css" rel="stylesheet" type="text/css"></link>

<script type="text/javascript">

var points_counter = {
	p: 0
}

var consent_trial = {
	type: "html",
	url: "/utils/consent.html",
	cont_btn: "agree2"
}

var instructions_trial = {
	type: "html",
	url: "/utils/instructions.html",
	cont_btn: "continue1"
}

var start_trial = {
	type: "html",
	url: "/utils/start.html",
	cont_btn: "start1"
}

var animdata = [98, 91, 101, 107, 107, 92, 98, 107, 109, 108, 101, 101, 104, 89, 101, 96, 100, 97, 106, 97, 96, 96, 98, 96, 106, 101, 105, 98, 96, 95, 101, 97, 97, 95, 102, 89, 103, 95, 108, 95, 97, 101, 111, 106, 107, 106, 101, 100, 109, 107];
var animanswers = [1, 2, 6, 5, 5, 1]; //[2, 6, 16, 12, 13, 1];

var animation_trial = {
	type: "number-animation",
	prices: animdata,
	phase: 0,
	continue_message: "Next step"
}

var training_trial = {
	type: "bar-choose",
	instructions: "Now let's imagine you would see 20 more tickets for your trip to Canada.",
	subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. <br><br>Press continue when you are sure of your answers.",
	categories: ["$85 - $90", "$91 - $95", "$96 - $100", "$101 - $105", "$106 - 110", "$111 - $115"],
	min_val: 0,
	max_val: 20,
	answers: animanswers
}

var testing_data = [];

var testing_instructions_trial = {
	type: "html",
        url: "/utils/testing.html",
        cont_btn: "testingstart"
}

// Second testing instructions (after example sequence)
var testing_instructions2_trial = {
	type: "html",
	url: "/utils/testing_after.html",
	cont_btn: "testingstart",
	on_finish: function() { $("#jspsych-points").css("opacity", "1"); }
}

// Second bar graph to see learning
var training_trial2 = {
	type: "bar-choose",
        instructions: "Now let's see if you understand the tickets better. Imagine you would see yet another 20 tickets to Canada.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
        categories: ["$85 - $90", "$91 - $95", "$96 - $100", "$101 - $105", "$106 - $110", "$111 - $115"],
        min_val: 0,
        max_val: 20,
	answers: animanswers
}

// Second training phase instructions
var p2_start_trial = {
	type: "html",
	url: "/utils/start2.html",
	cont_btn: "start2"
}

var animdata2 = [140, 97, 111, 113, 107, 130, 105, 139, 132, 94, 152, 80, 135, 124, 151, 105, 104, 126, 133, 162, 100, 100, 143, 122, 134, 138, 144, 130, 116, 143, 116, 143, 133, 136, 113, 115, 132, 75, 137, 136, 131, 144, 120, 142, 123, 75, 100, 109, 127, 109];
var animanswers2 = [1, 3, 4, 6, 5, 1]; //[3, 8, 10, 14, 12, 3];

// Second training phase
var p2_animation_trial = {
        type: "number-animation",
        prices: animdata2,
	phase: 1,
        continue_message: "Next step"
}

var p2_training_trial = {
        type: "bar-choose",
        instructions: "Now let's imagine you would see 20 more tickets for your trip to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
        categories: ["$75 - $90", "$91 - $105", "$106 - $120", "$121 - $135", "$136 - $150", "$151 - $165"],
        min_val: 0,
        max_val: 20,
	answers: animanswers2
}

var p2_testing_data = [];

var p2_testing_instructions_trial = {
        type: "html",
        url: "/utils/testing2.html",
        cont_btn: "testingstart",
	on_finish: function() {
		$("#jspsych-points").css("opacity", "1");
		$("#points-s").html("In sequence 1 out of 50");
	}
}

// Second bar graph to see learning
var p2_training_trial2 = {
        type: "bar-choose",
        instructions: "Now let's see if you understand the tickets better. Imagine you would see yet another 20 tickets to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
        categories: ["$75 - $90", "$91 - $105", "$106 - $120", "$121 - $135", "$136 - $150", "$151 - $165"],
        min_val: 0,
        max_val: 20,
	answers: animanswers2
}

var final_trial = {
	type: "final",
	points: function() { return points_counter.p; }
}

function preload()
{
	for(var i = 0; i < NUM_TICKETS; i++)
	{
		TICKET_IMAGES[0][i] = new Image();
		TICKET_IMAGES[0][i].src = "/utils/tickets/ticket" + (i+1) + ".jpg";
		TICKET_IMAGES[0][i].classList.add("ticket-img");

		TICKET_IMAGES[1][i] = new Image();
                TICKET_IMAGES[1][i].src = "/utils/tickets/2ticket" + (i+1) + ".jpg";
                TICKET_IMAGES[1][i].classList.add("ticket-img");
	}
}

preload();

function init()
{
	var timeline = [];


	$.post("/get.php", { f7g12d: "y" }, function(d) {

	var da = JSON.parse(d);
	testing_data = da[0];
	p2_testing_data = da[1];

//	timeline.push(final_trial);

	timeline.push(consent_trial);
        timeline.push(instructions_trial);
        timeline.push(start_trial);
//        timeline.push(animation_trial);
        timeline.push(training_trial);
        timeline.push(testing_instructions_trial);

	// example testing sequence
	timeline.push({ type: "ticket-choose",
			phase: 0,
			row: -1,
			prices: [184, 180, 224, 165, 181, 199, 185, 193, 218, 126],
			continue_mesage: "Finish",
			sequence: ""
	});

	timeline.push(testing_instructions2_trial);

	for(var i = 0; i < testing_data.length; i++)
	{
        	timeline.push({ type: "ticket-choose",
				prices: testing_data[i],
				row: i,
				phase: 0,
                       	        continue_message: "Next sequence",
				sequence: "In sequence <span>" + (i + 1) + "</span> out of <span>" + testing_data.length + "</span>",
			//	points: function() { return points_counter; },
				showpoints: true,
				on_finish: function(data) {
					$.post("/check.php", { phase: 0, sequence: data.sequence, answer: data.result }, function(d) {
					//	console.log(d);
						var da = JSON.parse(d);
						points_counter.p = da.points;
						$("#points-p").html(da.points);
					});
				}
		});
	}
	timeline[timeline.length-1].continue_message = "Finish";
	timeline[timeline.length-1].on_finish = function() { $("#jspsych-points").css("opacity", "0"); };

	timeline.push(training_trial2);

	timeline.push(p2_start_trial);
        timeline.push(p2_animation_trial);
        timeline.push(p2_training_trial);
        timeline.push(p2_testing_instructions_trial);

	for(var i = 0; i < p2_testing_data.length; i++)
        {
                timeline.push({ type: "ticket-choose",
				prices: p2_testing_data[i],
                                row: i,
				phase: 1,
				continue_message: "Next sequence",
                                sequence: "In sequence <span>" + (i + 1) + "</span> out of <span>" + p2_testing_data.length + "</span>",
                        //      points: function() { return points_counter; },
                                showpoints: true,
                                on_finish: function(data) {
					$.post("/check.php", { phase: 1, sequence: data.sequence, answer: data.result }, function(d) {
					//	console.log(d);
                                                var da = JSON.parse(d);
						points_counter.p = da.points;
                                                $("#points-p").html(da.points);
                                        });
                                }
                });
        }
        timeline[timeline.length-1].continue_message = "Finish";

	timeline.push(p2_training_trial2);

	timeline.push(final_trial);

	$("#wheel").css("display", "none");

	jsPsych.init({
		timeline: timeline,
		display_element: $("#jspsych-main")
	});

	});
}

</script>

</head>

<body onload="init()">
	<div class="wheel-loader-wrap" id="wheel"><div class="wheel-loader"></div></div>
	<div id="jspsych-points" style="opacity:0">
		<div class="points-main">
        		<div class="points-header">
               			<h2>Your Points:</h2>
        		</div>
                	<div class="points-points">
                       		<h2 id="points-p">0</h2>
                	</div>
                	<div class="points-subtitle">
                       		<p id="points-s">In sequence 1 out of 50</p>
                	</div>
		</div>
	</div>
	<div id="jspsych-main"></div>
</body>

</html>
