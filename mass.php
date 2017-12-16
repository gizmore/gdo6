<!DOCTYPE html>
<html>
<head>
<style type="text/css">
* { margin: 0; padding: 0; }
</style>
</head>
<body>
<canvas id="universe" width="1500" height="800" onmousedown="mousePressed(event)" onmouseup="mouseUp(event)"></canvas>
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
	this.X = x;
	this.MASS = mass;
}
function Space() {

    this.CANVAS = document.getElementById('universe');
    this.CONTEXT = this.CANVAS.getContext('2d');
	this.WIDTH = this.CANVAS.width;
	this.HEIGHT = this.CANVAS.height;
    this.DOWN = false;

    this.toX = function(px) {
        return this.renderX1() + (px/this.WIDTH) * this.renderW();
    };
    this.toPX = function(x) {
    	return ( (x - this.renderX1()) / (this.renderX2() - this.renderX1())) * this.renderPW();
    };

    this.toY = function(py) {
    	return this.renderY1() + (py/this.HEIGHT) * this.renderH();
    };
    this.toPY = function(y) {
    	return ( (y - this.renderY1()) / (this.renderY2() - this.renderY1())) * this.renderPH();
    };

	this.MASS = [];
	this.addMass = function(x, mass) {
		this.MASS.push(new Mass(x, mass));
	};


	this.renderY1 = function() {
		return -10;
	};
	this.renderY2 = function() {
		return 10;
	};

	this.renderH = function() {
		return this.renderY2() - this.renderY1();
	}	
	
	this.renderX1 = function() {
		var x = this.MASS[0].X; 
		for (var i in this.MASS) {
			var mass = this.MASS[i];
			if (mass.X < x) x = mass.X;
		}
		return x - 1.0;
	};
	this.renderX2 = function() {
		var x = this.MASS[0].X; 
		for (var i in this.MASS) {
			var mass = this.MASS[i];
			if (mass.X > x) x = mass.X;
		}
		return x + 1.0;
	};
	this.renderW = function() {
		return this.renderX2() - this.renderX1();
	};
	this.renderPW = function() {
		return this.WIDTH;
	}
	this.renderPH = function() {
		return this.HEIGHT;
	}

	this.tick = function() {
		for (var i in this.MASS) {
			var mass = this.MASS[i];
			mass.X += this.diff(mass.X);
		}
		this.render();
	};

	this.diff = function(x) {
		var dx = 0.0;
		for (var i in this.MASS) {
			var mass = this.MASS[i];
			dx += (x - mass.X) / mass.MASS;
// 			dx += mass.MASS / (x - mass.X) ;
		}
		return dx;
	};
	
	this.render = function() {
		this.renderClean();
		this.renderBG();
		this.renderMasses();
		this.renderSpace();
	};

	this.renderClean = function() {
		this.CONTEXT.clearRect(0, 0, this.CANVAS.width, this.CANVAS.height);
	};

	this.renderMasses = function() {
		for (var i in this.MASS) {
			this.renderMass(this.MASS[i]);
		}
	};

	this.renderMass = function(mass) {
		var ctx = this.CONTEXT;
		var x = this.toPX(mass.X);
// 		console.log(x);
		ctx.beginPath();
		ctx.arc(this.toPX(mass.X), this.toPY(0), mass.MASS*4, 0, 2*Math.PI);
		ctx.stroke();
		
	};
	this.renderBG = function() {
		var canvas = this.CANVAS;
		var context = this.CONTEXT;
		var y = canvas.height/2;
		context.beginPath();
		context.strokeStyle = '#003300';
		context.moveTo(0, y);
		context.lineTo(canvas.width, y);
		context.stroke();
	};

	this.renderSpace = function() {
		var context = this.CONTEXT;
		for (var i = 0; i < this.WIDTH; i++) {
			context.fillRect(i, this.toPY(this.diff(this.toX(i))), 1, 1);			
		}
	};
}
setTimeout(function(){
	
var space = new Space();
space.addMass(2, 2);
space.addMass(4, 2);
space.addMass(411, 5);
space.render();
// space.tick();
setInterval(space.tick.bind(space), 1000);
}, 100);
</script>
</body>
</html>
