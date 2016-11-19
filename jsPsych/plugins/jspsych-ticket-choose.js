jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
console.log("called");
		trial.prices = trial.prices || [];
		trial.continue_message = trial.continue_message || "Continue";
		trial.sequence = trial.sequence || "";

		var num_prices = trial.prices.length;
		if(!num_prices)
			jsPsych.finishTrial({ "result": "error" });

    		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.html("");
		display_element.load("/utils/ticket-choose.html", function()
		{
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
			below.html(trial.sequence);

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
					price.html("<span>$</span>" + trial.prices[price_num]).css("font-size", "150px");
					above.css("font-size", "35px").html("You chose the ticket with price:");
					below.html("");

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
					price.fadeOut(400, function() {
						select_price();
						price.fadeIn(400);
					});
				}
				else {
					price.animate({ transform: "translateX(30px)", opacity: "0" }, 200, function() {
						price.html("<span>$</span>" + trial.prices[price_num]).css("transform", "translateX(-30px)");
						price.animate({ transform: "translateX(0px)", opacity: "1" }, 200);

						above.html("Ticket number <span>" + (price_num + 1) + "</span> of <span>10</span>");
					});
				}
			}

			function end_trial()
                	{
				display_element.children().fadeOut(200);
                                jsPsych.pluginAPI.cancelAllKeyboardResponses();

                        	var trial_data = {
                                	"result": trial.prices[price_num]
                        	};

                        	jsPsych.finishTrial(trial_data);
                	}
		});
	}

  	return plugin;
})();
