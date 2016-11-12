jsPsych.plugins["ticket-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.prices = trial.prices || [];
		trial.num_trials = trial.num_trials || 0;

    		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		if(trial.prices.length != trial.num_trials)
		{
			console.log("Error in ticket choose plugin: the number of trials does not equal the number of price arrays given");
			end_trial();
		}

		var trial_num = 0;
		var price_num = 0;

		jsPsych.pluginAPI.getKeyBoardResponse({
			callback_function: step_price,
			valid_responses: [],
			rt_method: "date",
			persist: true,
			allow_held_key: false
		});

		function step_price(info)
		{
			if(price_num)
		}

		function end_trial()
		{
    			var trial_data = {
      				parameter_name: 'parameter value'
    			};

    			jsPsych.finishTrial(trial_data);
  		}
	};

  	return plugin;
})();
