(function($) {
	$.fn.barChooseGraph = function(type, categories, min, max)
	{
console.log(max);
		if(type == "init")
		{
			var nc = categories.length;
			var w = this.width();
			var cw = (100 / nc) + "%";
			var h = this.height();
			var root = this;

			var bgm = document.createElement("DIV");
			$(bgm).addClass("bar-graph-main");
			root.append(bgm);

			var bgg = document.createElement("DIV");
			$(bgg).addClass("bar-graph-graph");
			$(bgm).append(bgg);

			var bgmr = document.createElement("DIV");
                        $(bgmr).addClass("bar-graph-main-remaining");
                        $(bgmr).html("You have " + max + " tickets left to distribute");
                        $(bgm).append(bgmr);

			/*var bgcsw = document.createElement("DIV");
			$(bgcsw).addClass("bar-graph-columns-wrap");
			$(bgg).append(bgcsw);*/

			var bgcs = document.createElement("DIV");
			$(bgcs).addClass("bar-graph-columns");
			$(bgg).append(bgcs);

			var bgls = document.createElement("DIV");
			$(bgls).addClass("bar-graph-labels");
			$(bgg).append(bgls);

			var bglps = document.createElement("DIV");
			$(bglps).addClass("bar-graph-labels-texts");
			$(bgls).append(bglps);

			var bglis = document.createElement("DIV");
			$(bglis).addClass("bar-graph-labels-inputs");
			$(bgls).append(bglis);

			for(var i = 0; i < nc; i++)
			{
				var bgcw = document.createElement("DIV");
				$(bgcw).addClass("bar-graph-column-wrap");
				bgcw.id = "cwcat" + categories[i].replace(/\s/g, '');;
				$(bgcs).append(bgcw);

				var bgc = document.createElement("DIV");
				$(bgc).addClass("bar-graph-column");
				$(bgcw).append(bgc);
				bgc.id = "ccat" + categories[i].replace(/\s/g, '');
				bgc.value = min;
				bgc.style.height = bgc.style.minHeight;

				var bgli = document.createElement("DIV");
				$(bgli).addClass("bar-graph-input");
				bgli.style.width = cw;
				$(bgli).append("<input type='text' value='" + min + "' id='icat" + categories[i] + "'></input>");
				$(bglis).append(bgli);

				var bglp = document.createElement("P");
				bglp.style.width = cw;
				bglp.innerHTML = categories[i];
			 	$(bglps).append(bglp);
			}

			var remaining = max;

			function checkRest()
			{
				remaining = max;
				$(root).find(".bar-graph-column").each(function() {
					remaining -= this.value;
				});

				$(bgmr).html("You have " + remaining + " tickets left to distribute");
			}

			window.setTimeout(function() {
				$(root).find(".bar-graph-column").each(function() {
					var bgc = this;

					$(bgc).on("mousedown", function (e)
					{
                                        	var sh = $(bgc).height();
                                        	var sy = e.pageY;
						var maxh = bgc.parentNode.clientHeight;

                                        	$(document).on("mouseup", function(me) {
                                                	$(document).off("mouseup").off("mousemove");
                                        	});

                                        	$(document).on("mousemove", function(me) {
							var cat = bgc.id.substr(4, bgc.id.length - 4);
                                                	var my = (me.pageY - sy);

							checkRest();
							var v = parseInt((max - min) * (sh - my) / maxh + min).clamp(min, remaining);

							$(bgc).css("height", (maxh * (v - min) / (max - min)) + "px");
							bgc.value = v;
							$(root).find("#icat" + cat).val(v);
                                        	});
					});
                                });

				$(root).find(".bar-graph-input input").each(function() {
					var self = this;
					$(this).change(function() {
						var cat = self.id.substr(4, self.id.length - 4);

						checkRest();
						var v = parseInt(self.value).clamp(min, remaining) || min;
						var bar = $(root).find("#ccat" + cat)[0];
						var maxh = bar.parentNode.clientHeight;

						bar.style.height = (maxh * (v - min) / (max - min)) + "px";
						bar.value = v;
						self.value = v;
					});
				});
			}, 100);
		}
		else if(type == "get")
		{
			
		}

		return this;
	};
}(jQuery));
