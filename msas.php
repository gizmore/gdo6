<!DOCTYPE html>
<html>
<head>
<style type="text/css">
* {
margin: 0;
padding: 0;
}
#universe {
}
</style>


</head>
<body>
<canvas id="universe" width="1500" height="800" onmousedown="mousePressed(event)" onmouseup="mouseUp(event)">
</canvas>


<script type="text/javascript">
function mousePressed(event) {
	space.DOWN = true;
	space.DOWN_X = event.clientX;
	console.log(event);
};
function mouseUp(event) {
	space.DOWN = false;
	console.log(event);
};

function Mass(x, mass) {
	console.log("Mass.new()", x, mass);
	this.X = x;
	this.VX = 0;
	this.MASS = mass;
};

function Space() {
	this.MASSES = [];

	this.ADDING_MASS = null;

	this.addMass = function(pos, mass) {
		var m = new Mass(pos, mass);
		this.MASSES.push(m);
	};

	this.tick = function() {


		// Tick addmass
		if (this.DOWN) {
			if (!this.ADDING_MASS) {
				this.ADDING_MASS = new Mass(this.spx(this.DOWN_X), 1.0);
			}
			else {
				this.ADDING_MASS.MASS *= 1.035;
			} 
		}
		else if (this.ADDING_MASS) {
			this.MASSES.push(this.ADDING_MASS);
			this.ADDING_MASS = null;
		}

		
		this.draw();

	};


	this.minX = function() { return 0; };
	this.maxX = function() { return 1000; };
	this.maxG = function() { return 10; };
	this.minG = function() { return -this.maxG(); };

	this.SW = 1000;
	this.SH = 200;
	this.sw = function() { return this.SW; };
	this.sh = function() { return this.SH; };

	this.px = function(x) { return ( (x - this.minX()) / (this.maxX() - this.minX())) * this.sw(); };
	this.py = function(y) { return ( (y - this.minG()) / (this.maxG() - this.minG())) * this.sh(); };

	this.spx = function(sx) {
		console.log('SPX', sx);
		console.log((this.maxX() - this.minX()) * (sx / this.SW) + this.minX());
		return (this.maxX() - this.minX()) * (sx / this.SW) + this.minX();  
	};
	
	this.draw = function() {
	      var canvas = document.getElementById('universe');
	      var context = canvas.getContext('2d');
	      this.SW = canvas.width;
	      this.SH = canvas.height;
	      var centerX = canvas.width / 2;
	      var centerY = canvas.height / 2;

	      var minX = this.minX();
	      var maxX = this.maxX();

	      context.clearRect(0, 0, canvas.width, canvas.height);

	      // Draw Kartesian
		  context.beginPath();
	      context.strokeStyle = '#003300';
	      context.moveTo(this.px(0),this.py(0));
	      context.lineTo(this.px(1000),this.py(0));
	      context.stroke();

	      // Draw masses
	      for (var i in this.MASSES) {
	    	  this.drawMass(context, this.MASSES[i]);
	      }
	      if (this.ADDING_MASS) {
		  	this.drawMass(context, this.ADDING_MASS);
	      }


	      // Tick masses
	      for (var i in this.MASSES) {
		      this.tickMass(this.MASSES[i]);
	      }
	      for (var i in this.MASSES) {
		      var mass = this.MASSES[i];
// 		      mass.X += Math.clamp(mass.VX, -1, 1);
// 		      mass.X += Math.clamp(mass.VX, -100, 100);
		      mass.X += mass.VX;
		      mass.VX = 0;
	      }

	      // Tick lamda
// 	      for (var i in this.MASSES) {
// 	    	  this.MASSES[i].X *= 1.000001123;
// 		  }
	      
	      // Draw differential space bending curve
	      for (var x = 0; x < this.SW; x++) {
	    	  context.fillRect(this.px(x), this.py(this.diff(x)), 1, 1 );			
	      }

	};

	this.tickMass = function(mass) {

		mass.VX = this.diff(mass.X);

// 		for (var i in this.MASSES) {

// 			var oMass = this.MASSES[i];

// 			if (mass != oMass) {
// 				var l = oMass.X - mass.X;
// 				var m =  oMass.MASS;
// // 				if (l)
// 				if (Math.abs(l)>=1)
// 					mass.VX += m/l;
// 			}

// 		}
	};

	this.diff = function(x) {

		var f = 0.0;
		for (var i in this.MASSES) {
			var mass = this.MASSES[i];
			f += (mass.X - x) / mass.MASS;
// 			f += mass.MASS / (mass.X - x);
		}
		return f;      
			



		


		
		var f = 0.0;
		for (var i in this.MASSES) {
			var oMass = this.MASSES[i];
			
			var l = oMass.X - x;
			var m = oMass.MASS;

			if (Math.abs(l)>0)
			f += m/l;
			
			
			
// 			f += Math.clamp(m/l, -m, m); //-Math.abs(l);

// // 			f += Math.clamp(m/l, -m, m);
			
// 			var l = oMass.X - x+0.02;
// 			var m = oMass.MASS;

// 			f += Math.clamp(m/l, -m, m); //-Math.abs(l);
		}
		return f;
	};
	
	this.drawMass = function(ctx, mass) {
		ctx.beginPath();
		ctx.arc(this.px(mass.X), this.py(0), mass.MASS/5, 0, 2*Math.PI);
		ctx.stroke();
	};
}

var space = new Space();
space.addMass(100, 20);
space.addMass(900, 100);

for (var i = -10; i <= 10; i++) {
// 	space.addMass((i+10)*50, i==0?20:5);
}
setInterval(space.tick.bind(space), 1);

Math.clamp = function(v, min, max) {
	if ((min !== undefined) && (v < min)) { return min; }
	if ((max !== undefined) && (v > max)) { return max; }
	return v;
};
</script>

</body>
</html>

<?php
