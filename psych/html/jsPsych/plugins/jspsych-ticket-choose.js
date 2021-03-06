jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.prices = trial.prices || [];
		trial.continue_message = trial.continue_message || "Continue";
		trial.sequence = trial.sequence || "";
		trial.showpoints = trial.showpoints || false;
		trial.phase = trial.phase || 0;

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

    		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.html("");

		display_element.load("/utils/ticket-choose.html", function()
		{
			var price_num = -1;

			if(trial.showpoints)
				$("#points-s").html(trial.sequence);

			var price = display_element.find(".number-animation");
			next_price();

			var select = display_element.find("#ticket-choose-select");
			select.click(select_price);

			var next = display_element.find("#ticket-choose-next");
			next.click(next_price);

			var above = display_element.find(".number-animation-above");
			var below = display_element.find(".number-animation-below");

			var listener = jsPsych.pluginAPI.getKeyboardResponse({
				callback_function: next_price,
				valid_responses: [32],
				rt_method: "date",
				persist: true,
				allow_held_key: false
			});

			var selected = false;

			function select_price()
			{
				if(price_num < num_prices)
				{
					display_element.find(".ticket-choose-main").css("opacity", "0");

					jsPsych.pluginAPI.cancelKeyboardResponse(listener);

					setTimeout(function() {

					var prices = trial.prices.slice(0);
	                                prices.sort(function(a, b){return a - b});

					//console.log(prices);
					//console.log(trial.prices[price_num]);

					var points = 0;

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
						below.html("You now have a total of " + (pr + points) + ((pr+points) === 1 ? " point." : " points."));

					$("#ticket-wrap").hide();

					listener = jsPsych.pluginAPI.getKeyboardResponse({
                                		callback_function: function() { end_trial(points, r); },
                                		valid_responses: [32],
                                		rt_method: "date",
                                		persist: true,
                                		allow_held_key: false
                        		});

					select.hide();
					next.html(trial.continue_message).addClass("big-btn").off("click").click(function() { end_trial(points, r); });

					selected = true;

					display_element.find(".ticket-choose-main").css("opacity", "1");

					}, 200);
				}
				else
					end_trial(0, -1);
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
				else if(price_num === 0) {
					price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
                                       	showTicket(trial.phase, $("#ticket-wrap"));
                                       	price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);

					display_element.find(".ticket-choose-main").css("opacity", "1");
				}
				else {
					price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
						price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
						price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);
						showTicket(trial.phase, $("#ticket-wrap"));
						above.html("Ticket <span>" + (price_num + 1) + "</span> of <span>10</span>:");
					});
				}
			}

			function end_trial(ps, r)
                	{
				if(r == -1)
					return;

				display_element.find(".ticket-choose-main").css("opacity", "0");
                                jsPsych.pluginAPI.cancelAllKeyboardResponses();
//console.log("answer: " + trial.prices[price_num]);
                        	var trial_data = {
                                	"result": trial.prices[price_num],
					"points": ps,
					"place": r,
					"phase": trial.phase,
					"sequence": trial.row
                        	};

                        	jsPsych.finishTrial(trial_data);
                	}
		});
	}

  	return plugin;
})();
