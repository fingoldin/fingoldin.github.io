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

		$(bot).css("opacity", "0");
		$(top).css("opacity", "0").animate({ "opacity": "1" }, 1000, function() {
			setTimeout(function() {
				$(bot).animate({ "opacity": "1" }, 1000);
				setTimeout(function() {
					$(wrap).animate({ "opacity": "0" }, 600, function() {
						tfs(40);
						bfs(50);
						$(bot).css("opacity", "0");

						/*var sbot = [];
						sbot[0] = trial.points;
						sbot[1] = " * ";
						sbot[2] = "0.025";
						sbot[3] = " = ";
						sbot[4] = "$" + (trial.points * 0.025);*/

						bot.innerHTML = trial.points + " * 0.025 = $" + (trial.points * 0.025);
						top.innerHTML = "You scored " + trial.points + (trial.points === 1 ? " point" : " points") + ", and receive";

						$(wrap).animate({ "opacity": "1" }, 600, function() {
							setTimeout(function() {
								//$(bot).css("opacity", "1");

								$(bot).animate({ "opacity": "1" }, 600);

								setTimeout(function() {
									$(top).animate({ "opacity": "0" }, 400, function() {
										$(top).html("Thanks for participating!");
										$(top).animate({ "opacity": "1" }, 400);
									});
								}, 3000);
							}, 800);
						});
					});
				}, 3000);
			}, 1500);
		});
	}

	return plugin;
})();
