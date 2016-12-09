jsPsych.plugins["final"] = (function()
{
	var plugin = {};

	plugin.trial = function(display_element, trial)
	{
		trial.points = trial.points || 0;

		trial = jsPsych.pluginAPI.evaluateFunctionParameters(trial);

		display_element.empty();


		var wrap = document.createElement("DIV")
		var top = document.createElement("DIV");
		var bot = document.createElement("DIV");

		$(wrap).append(top).append(bot);
		display_element.append(wrap);

		function tfs(s) {
			top.style.fontSize = s + "px";
			top.style.lineHeight = Math.floor(1.4 * s) + "px";
		}

		function bfs(s) {
                        bot.style.fontSize = s + "px";
                        bot.style.lineHeight = Math.floor(1.4 * s) + "px";
                }

		tfs(55);
		bfs(35);

		top.innerHTML = "Congratulations!";
		bot.innerHTML = "The experiment is now over.";

		$(bot).hide();
		$(top).hide().fadeIn(1000, function() {
			setTimeout(function() {
				$(bot).fadeIn(1000);
				setTimeout(function() {
					$(wrap).fadeOut(1000, function() {
						bot.innerHTML = "";
						tfs(40);
						bfs(50);
						$(bot).hide();
						bot.innerHTML = trial.points + " * 0.025 = $" + (trial.points * 0.025);
						top.innerHTML = "You scored " + trial.points + (trial.points === 1 ? " point" : " points") + ", and receive";
						$(wrap).fadeIn(400, function() {
							$(bot).fadeIn(1000);
						});
					});
				}, 4000);
			}, 1500);
		});
	}

	return plugin;
})();