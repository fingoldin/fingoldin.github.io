(function($) {
	$.fn.barChooseGraph = function(type, data)
	{
//console.log(max);
		if(type == "init")
		{
			var nc = data.categories.length;
			var w = this.width();
			var cw = (100 / nc) + "%";
			var h = this.height();
			var root = this;

			$(root).data("categories", data.categories);

			var bgm = document.createElement("DIV");
			$(bgm).addClass("bar-graph-main");
			root.append(bgm);

			var bgg = document.createElement("DIV");
			$(bgg).addClass("bar-graph-graph");
			$(bgm).append(bgg);

			var bgmr = document.createElement("DIV");
                        $(bgmr).addClass("bar-graph-main-remaining");
                        $(bgmr).html("You have <span>" + data.max + "</span> tickets left to distribute");
                        $(bgg).append(bgmr);

			var bgcow = document.createElement("DIV");
			$(bgcow).addClass("bar-graph-congrats-wrap");
			bgcow.id = "bar-graph-cw";
			bgcow.innerHTML = "<div class='bar-graph-congrats'><div class='bar-graph-congrats-top'>Hell Yeah!</div><div class='bar-graph-congrats-bottom'>You got this many right</div></div>";
			$(bgg).append(bgcow);

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
				bgcw.id = "cwcat" + i;
				$(bgcs).append(bgcw);

				var bgc = document.createElement("DIV");
				$(bgc).addClass("bar-graph-column");
				$(bgcw).append(bgc);
				bgc.id = "ccat" + i;
				$(bgc).data("value", data.min);

				var bgca = document.createElement("DIV");
				$(bgca).addClass("bar-graph-column-a");
				$(bgcw).append(bgca);
				bgca.id = "cacat" + i;

				var bgli = document.createElement("DIV");
				$(bgli).addClass("bar-graph-input");
				bgli.style.width = cw;
				$(bgli).append("<input type='text' value='" + data.min + "' id='icat" + i + "'></input>");
				$(bglis).append(bgli);

				var bglp = document.createElement("P");
				bglp.style.width = cw;
				bglp.innerHTML = data.categories[i];
			 	$(bglps).append(bglp);
			}

			var remaining = data.max;

			function checkRest()
			{
				remaining = data.max;
				$(root).find(".bar-graph-column").each(function() {
					remaining -= parseInt($(this).data("value"));
				});

				$(bgmr).find("span").html(remaining);
			}

			window.setTimeout(function() {
				$(root).find(".bar-graph-column").each(function() {
					var bgc = this;

					$(bgc).on("mousedown", function (e)
					{
                                        	var sh = $(bgc).height();
                                        	var sy = e.pageY;
						var maxh = bgc.parentNode.clientHeight;
						var minh = 15;

                                        	$(document).on("mouseup", function(me) {
                                                	$(document).off("mouseup").off("mousemove");
                                        	});

                                        	$(document).on("mousemove", function(me) {
							var cat = bgc.id.substr(4, bgc.id.length - 4);
                                                	var my = (me.pageY - sy);
							var input = $(root).find("#icat" + cat)[0];

							var v = parseInt((data.max - data.min) * (sh - my) / maxh + data.min).clamp(data.min, data.max);
							var pv = parseInt($(bgc).data("value"));
							//console.log(v + " " + (pv + remaining));
							if(v >= (pv + remaining)) {
								v = pv + remaining;

								//$(bgmr).find("span").addClass("highlight");
								//window.setTimeout(function() { $(bgmr).find("span").removeClass("hightlight"); }, 100);
							}

							$(bgc).css("height", parseInt((maxh - minh) * (v - data.min) / (data.max - data.min) + minh) + "px");
							$(bgc).data("value", v);
							input.value = v;
							checkRest();
                                        	});
					});
                                });

				$(root).find(".bar-graph-input input").each(function() {
					var self = this;
					$(this).change(function() {
						var cat = self.id.substr(4, self.id.length - 4);
						var bar = $(root).find("#ccat" + cat)[0];

						var v = (parseInt(self.value) || data.min).clamp(data.min, data.max);
						var pv = parseInt($(bar).data("value"));
						if(v >= (pv + remaining)) {
                                                	v  = pv + remaining;

                                                        //$(bgmr).find("span").addClass("highlight");
                                                        //window.setTimeout(function() { $(bgmr).find("span").removeClass("hightlight"); }, 100);
                                                }

						var maxh = bar.parentNode.clientHeight;
						var minh = 15;

						bar.style.height = parseInt((maxh - minh) * (v - data.min) / (data.max - data.min) + minh) + "px";
						$(bar).data("value", v);
						self.value = v;
						checkRest();
					});
					$(this).focus(function() {
						self.select();
					});
				});
			}, 100);
		}
		else if(type == "get")
		{
			var vals = $(this).find(".bar-graph-column");
			var cats = $(this).data("categories");
			var data = [];

			for(var i = 0; i < vals.length && i < cats.length; i++)
				data.push([ parseInt($(vals[i]).data("value")), cats[i] ]);
console.log(data);
			return data;
		}
		else if(type == "show")
		{
			var root = this;

			$(root).find(".bar-graph-input input").off("change").prop("disabled", true);
			$(root).find(".bar-graph-column").off("mousedown").css("opacity", "0.4");

			var acols = $(root).find(".bar-graph-column-a");
			for(var i = 0; i < acols.length; i++)
			{
				var maxh = acols[i].parentNode.clientHeight;
				var h = parseInt(maxh * data.answers[i] / data.max) + "px";

				$(acols[i]).css("opacity", "0.5").css("height", h);

				window.setTimeout(function() {
					$(root).find("#bar-graph-cw").css("transform", "scale(1, 1)");
				}, 500);
			}
		}

		return this;
	};
}(jQuery));
