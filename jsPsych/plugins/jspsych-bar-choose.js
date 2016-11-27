jsPsych.plugins["bar-choose"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.instructions = trial.instructions || "No instructions given";
		trial.subtitle = trial.subtitle || "";
		trial.categories = trial.categories || [];
		trial.min_val = trial.min_val || 0;
		trial.max_val = trial.max_val || 100;
		trial.points = trial.points || { points: 0, subtitle: "" };

		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

		display_element.load("/utils/bar-choose.html", function()
		{
			showPoints(display_element, trial.points);

			display_element.find("#bar-instructions").html(trial.instructions);
			display_element.find("#bar-subtitle").html(trial.subtitle);

			display_element.find("#bar-graph").height(600).barChooseGraph("init", trial.categories, trial.min_val, trial.max_val);

			display_element.find("#bar-submit").click(function() {
				display_element.html("");

				var data = {
					responses: display_element.find("#bar-graph").barChooseGraph("get")
				}

				jsPsych.finishTrial(data);
			});
		});
	}

	return plugin;
})();
