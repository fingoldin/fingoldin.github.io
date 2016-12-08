jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.prices = trial.prices || [];
		trial.continue_message = trial.continue_message || "Continue";
		trial.sequence = trial.sequence || "";
		trial.showpoints = trial.showpoints || false;

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

    		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.html("");
		display_element.load("/utils/ticket-choose.html", function()
		{
//			if(trial.points)
//				showPoints(display_element, trial.points, trial.sequence);

			var price_num = -1;

/*	var wrap = display_element.find("#jspsych-animation-image");
      $(wrap).html("");
      $(wrap).append("<div class='number-animation-above'>Price of ticket:</div>");

      var number = document.createElement("DIV");
      number.classList.add("number-animation");
      number.innerHTML = "<span>" + trial.prefix + "</span>" + trial.stimuli[animate_frame];
      $(wrap).append(number);

      $(number).css("transform", "translateX(0px)").css("opacity", "0");
      $(number).stop().animate({ transform: "translateX(0px)", opacity: "1" }, interval_time / 2, function() {
        $(number).stop().animate({ transform: "translateX(0px)", opacity: "0" }, interval_time / 2);
      });*/


			var price = display_element.find(".number-animation");
			next_price();

			var select = display_element.find("#ticket-choose-select");
			select.click(select_price);

			var next = display_element.find("#ticket-choose-next");
			next.click(next_price);

			var above = display_element.find(".number-animation-above");
			var below = display_element.find(".number-animation-below");

			$("#points-s").html(trial.sequence);

			var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: next_price,
				valid_responses: [32],
				rt_method: "date",
				persist: true,
				allow_held_key: false
			});

			var selected = false;
			var points = 0;

			function select_price()
			{
				if(price_num < num_prices)
				{
					var prices = trial.prices.slice(0);
	                                prices.sort(function(a, b){return a - b});

					console.log(prices);
					console.log(trial.prices[price_num]);

					var r = prices.indexOf(trial.prices[price_num]);
                                	if(r === 0)
                                        	points = 2;
                                	else if(r === 1)
                                        	points = 1;

					//console.log(points);

					var pr = parseInt($("#points-p").html());
					if(trial.showpoints && points)
					{
						$("#points-p").fadeOut(150, function() {
							$("#points-p").html(pr + points).fadeIn(150);
						});
					}

					var am = "";
					if(r === 0)
						am = "You chose the best ticket!";
					else if(r === 1)
						am = "You chose the 2nd best ticket!";
					else if(r === 2)
						am = "You chose the 3rd best ticket.";
					else
						am = "You chose the " + r + "th best ticket.";

					if(trial.showpoints)
					{
						if(points == 1)
							am = am.slice(0, -1).concat(" and get 1 point.");
						else if(points == 2)
							am = am.slice(0, -1).concat(" and get 2 points.");
						else
							am = am.slice(0, -1).concat(" and get " + points + " points.");
					}

					price.hide();
                                        above.html(am);
					if(trial.showpoints)
						below.html("You now have a total of " + (pr + points) + ((pr+points) === 1 ? " point" : " points") + " out of 100.");

					$("#ticket-wrap").hide();

					jsPsych.pluginAPI.cancelKeyboardResponse(listener);

					select.hide();
					next.html(trial.continue_message).addClass("big-btn").off("click").click(end_trial);

					selected = true;
				}
			}

			function next_price()
			{
//				if(selected)
//					end_trial();
				if(price.is(":animated"))
					return;
				else if(++price_num >= num_prices) {
					price_num = num_prices - 1;
					select_price();
				}
				else {
					price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
						price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
						price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
						showTicket($("#ticket-wrap"));
						above.html("Ticket <span>" + (price_num + 1) + "</span> of <span>10</span>:");
					});
				}
			}

			function end_trial()
                	{
				display_element.children().fadeOut(200);
                                jsPsych.pluginAPI.cancelAllKeyboardResponses();

                        	var trial_data = {
                                	"result": trial.prices[price_num],
					"points": points
                        	};

                        	jsPsych.finishTrial(trial_data);
                	}
		});
	}

  	return plugin;
})();
