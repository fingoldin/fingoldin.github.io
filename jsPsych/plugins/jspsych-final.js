jsPsych.plugins["final"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.points = trial.points || 0;

		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();

		var top = document.createElement("DIV");
		var bot = document.createElement("DIV");

		display_element.append(top).append(bot);

		bot.style.fontSize = "55px";
		top.style.fontSize = "35px";

		top.innerHTML = "Congratulations!";
		bot.innerHTML = "The experiment is now over.";
	}

	return plugin;
})();
