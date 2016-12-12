<?php

require("../includes.php");

startSession();

?>

<!DOCTYPE html>

<html>

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

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.5, minimum-scale=0.8, user-scalable=yes">

<title>Psychology experiment</title>

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

var animation_trial = {
	type: "number-animation",
	//prices: animdata,
	phase: 0,
	continue_message: "Next step"
}

var training_trial = {
	type: "bar-choose",
	instructions: "Now let's imagine you would see 20 more tickets for your trip to Canada.",
	subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. <br><br>Press continue when you are sure of your answers.",
	categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
	min_val: 0,
	max_val: 20,
	phase: 0,
	number: 0
	//answers: animanswers
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
	on_finish: function() { $("#jspsych-points").css("display", "block"); }
}

// Second bar graph to see learning
var training_trial2 = {
	type: "bar-choose",
        instructions: "Now let's see if you understand the tickets better. Imagine you would see yet another 20 tickets to Canada.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
        categories: ["$135 - $150", "$151 - $165", "$166 - 180", "$181 - $195", "$196 - $210", "$211 - $225", "$226 - $240"],
        min_val: 0,
        max_val: 20,
	phase: 0,
	number: 1
	//answers: animanswers
}

// Second training phase instructions
var p2_start_trial = {
	type: "html",
	url: "/utils/start2.html",
	cont_btn: "start2"
}

// Second training phase
var p2_animation_trial = {
        type: "number-animation",
        //prices: animdata2,
	phase: 1,
        continue_message: "Next step"
}

var p2_training_trial = {
        type: "bar-choose",
        instructions: "Now let's imagine you would see 20 more tickets for your trip to Mexico City.",
        subtitle: "Please drag the bar or type in the input field to determine the amount of tickets that are in the equivalent price range for this trip. Press continue when you are sure of your answers.",
        categories: ["$105 - $130", "$131 - $155", "$156 - $180", "$181 - $205", "$206 - $230", "$231 - $255", "$256 - $280"],
        min_val: 0,
        max_val: 20,
	phase: 1,
	number: 0
	//answers: animanswers2
}

var p2_testing_data = [];

var p2_testing_instructions_trial = {
        type: "html",
        url: "/utils/testing2.html",
        cont_btn: "testingstart",
	on_finish: function() {
		$("#jspsych-points").css("display", "block");
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
	phase: 1,
	number: 1
	//answers: animanswers2
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

	var animdata = [];
	var animanswers = [];
	var animdata2 = [];
	var animanswers2 = [];

	var da = JSON.parse(d);
	testing_data = da["testing"][0];
	p2_testing_data = da["testing"][1];
	animdata = da["training"][0];
	animdata2 = da["training"][1];
	animanswers = da["answers"][0];
	animanswers2 = da["answers"][1];

	animation_trial.prices = animdata;
	p2_animation_trial.prices = animdata2;
	training_trial.answers = animanswers;
	training_trial2.answers = animanswers;
	p2_training_trial.answers = animanswers2;
	p2_training_trial2.answers = animanswers2;

//	timeline.push(final_trial);

	timeline.push(consent_trial);
        timeline.push(instructions_trial);
        timeline.push(start_trial);
        timeline.push(animation_trial);
        timeline.push(training_trial);
        //timeline.push(testing_instructions_trial);

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
						console.log(d);
						var da = JSON.parse(d);
						points_counter.p = da.points;
						$("#points-p").html(da.points);
					});
				}
		});
	}
	timeline[timeline.length-1].continue_message = "Finish";
	timeline[timeline.length-1].on_finish = function(data) {
		$.post("/check.php", { phase: 0, sequence: data.sequence, answer: data.result }, function(d) {
                        console.log(d);
                        var da = JSON.parse(d);
                        points_counter.p = da.points;
        	        $("#points-p").html(da.points);
                });

		$("#jspsych-points").css("display", "none");
	};

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
						console.log(d);
                                                var da = JSON.parse(d);
						points_counter.p = da.points;
                                                $("#points-p").html(da.points);
                                        });
                                }
                });
        }
        timeline[timeline.length-1].continue_message = "Finish";
        timeline[timeline.length-1].on_finish = function(data) {
                $.post("/check.php", { phase: 1, sequence: data.sequence, answer: data.result }, function(d) {
                        console.log(d);
                        var da = JSON.parse(d);
                        points_counter.p = da.points;
                        $("#points-p").html(da.points);
                });

                $("#jspsych-points").css("display", "none");
        };

	timeline.push(p2_training_trial2);

	timeline.push(final_trial);

	$("#wheel").css("display", "none");

	jsPsych.init({
		timeline: timeline,
		display_element: $("#jspsych-main"),
		on_finish: function(data) {
			$("#jspsych-main").html("<div class='thanks'>Thank you for participating!</div>");

			$.post("/submit.php", { data: JSON.stringify(data) }, function(d) { console.log(d); });
		}
	});

	});
}

</script>

</head>

<body onload="init()">
	<div class="wheel-loader-wrap" id="wheel"><div class="wheel-loader"></div></div>
	<div id="jspsych-points" style="display:none">
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
