Number.prototype.clamp = function(min, max) {
  return Math.min(Math.max(this, min), max);
};

function showPoints(e, p)
{
	e.load("/utils/points.html", function()
	{
		e.find("#points-p").html(p.points);
		e.find("#points-s").html(p.subtitle);
	});
}

function gaussian(mean, stdev)
{
    var y2;
    var use_last = false;
    return function() {
        var y1;
        if(use_last) {
           y1 = y2;
           use_last = false;
        }
        else {
            var x1, x2, w;
            do {
                 x1 = 2.0 * Math.random() - 1.0;
                 x2 = 2.0 * Math.random() - 1.0;
                 w  = x1 * x1 + x2 * x2;
            } while( w >= 1.0);
            w = Math.sqrt((-2.0 * Math.log(w))/w);
            y1 = x1 * w;
            y2 = x2 * w;
            use_last = true;
       }

       var retval = mean + stdev * y1;
       if(retval > 0) 
           return retval;
       return -retval;
   }
}

function GAnimData(mean, variance, min, max, num)
{
	this.mean = mean;
	this.variance = variance;
	this.min = min;
	this.max = max;
	this.num = num;

	this.g = gaussian(this.mean, Math.sqrt(this.variance));
	this.data = [];

	var i = 0;
	while(i < this.num)
	{
		var x = parseInt(this.g());
		if(x >= this.min && x <= this.max)
		{
			this.data.push(x);
			i++;
		}
	}
}


function GAnimData2(min, max, num, clamp) {
        this.min = min;
        this.max = max;
        this.num = num;
        this.clamp = clamp;
        this.data = [];

        this.mean = Math.random() * (this.max - this.min) * (1 - this.clamp * 2) + (this.min + (this.max - this.min) * this.clamp);
        this.stdev = Math.min(this.mean - this.min, this.max - this.mean) / 2;
        this.g = gaussian(this.mean, this.stdev);

        console.log("Generated data with mean: " + this.mean + " and standard deviation: " + this.stdev);

        var i = 0;
        while(i < this.num) {
                var x = parseInt(this.g());
                if(x >= this.min && x <= this.max) {
                        this.data.push(x);
                        i++;
                }
        }
}

