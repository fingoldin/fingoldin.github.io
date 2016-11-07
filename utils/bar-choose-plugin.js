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
				bgcw.style.width = cw;
				bgcw.id = "cwcat" + categories[i];
				$(bgcs).append(bgcw);

				var bgc = document.createElement("DIV");
				$(bgc).addClass("bar-graph-column");
				$(bgcw).append(bgc);
				bgc.id = "ccat" + categories[i];
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

			window.setTimeout(function() {
				$(".bar-graph-column").each(function() {
					var bgc = this;

					$(bgc).on("mousedown", function (e)
					{
                                        	var sh = $(bgc).height();
                                        	var sy = e.pageY;
						var maxh = bgc.parentNode.clientHeight;
						var minh = parseInt(bgc.style.minHeight);
console.log(minh);
                                        	$(document).on("mouseup", function(me) {
                                                	$(document).off("mouseup").off("mousemove");
                                        	});

                                        	$(document).on("mousemove", function(me) {
							var cat = bgc.id.substr(4, bgc.id.length - 4);
                                                	var my = (me.pageY - sy);
							var v = parseInt((max - min) * (sh - my) / maxh + min).clamp(min, max);

							$(bgc).css("height", ((maxh - minh) * (v - min) / (max - min) + minh) + "px");
							bgc.value = v;
							$(root).find("#icat" + cat).val(v);
                                        	});
					});
                                });

				$(".bar-graph-input input").each(function() {
					var self = this;
					$(this).change(function() {
						var cat = self.id.substr(4, self.id.length - 4);
						var v = parseInt(self.value).clamp(min, max) || min;
						var bar = $(root).find("#ccat" + cat)[0];
						var maxh = bar.parentNode.clientHeight;
						var minh = parseInt(bar.style.minHeight);
console.log(minh);
						bar.style.height = ((maxh - minh) * (v - min) / (max - min) + minh) + "px";
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
