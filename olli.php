<!DOCTYPE html>
<html>
<head>
<style type="text/css">
#world { width: 1400px; height: 900px; }
</style>
</head>
<body>
<canvas id="world" width="1400" height="900"></canvas>
<script type="text/javascript" src="GDO/JQuery/bower_components/jquery/dist/jquery.js"></script>

<script type="text/javascript">
var WALKS = 100;
var walks = [];
var canvas = document.getElementById("world");
var context = canvas.getContext("2d");
function walk_rand1f_algo()
{
	return (Math.random() - 0.5) * 1;
}

function walk_rand1_algo()
{
	return Math.random() > 0.5 ? 1 : -1;
}
function walk_rand10_algo()
{
	var r = Math.random();
	if (r < 0.33) {
		return -1; 
	}
	else if (r < 0.66) {
		return 1;
	}
	else {
		return 0;
	}
}

function sx(x) { return x; }
function sy(y) { return 450 + y; }
function x(sx) { return sx; }
function y(sy) { return sy; }

function Walk()
{
	this.steps = [0];
	this.step = 0;
	this.max = 0;
	this.min = 0;

	this.lastValue = function() { return this.steps[this.step]; };
	this.prevValue = function() { return this.steps[this.step-1]; };
	this.lastDiff = function() { return this.lastValue() - this.prevValue(); };

	this.walk = function(algorithm) {
		var diff = algorithm();
		this.steps[this.step+1] = this.steps[this.step] + diff;
		this.step++;
	}
}

function drawDot(walk) {
	context.beginPath();
	context.moveTo(sx(walk.step-1), sy(walk.prevValue()));
	context.lineTo(sx(walk.step), sy(walk.lastValue()));
	context.stroke();
}
function drawIntermediate(x, walks, n) {
	var median = 0;
	for (var i = 0; i < n; i++) {
		median += walks[i].steps[x];
	}
	context.lineTo(sx(x), sy(median/n));
}

$(function(){
	for (var i = 0; i < WALKS; i++) {
		var walk = new Walk();
		for (var x = 0; x < 1400; x++) {
			walk.walk(walk_rand1_algo);
			drawDot(walk);
		}
		walks.push(walk);	
	}

	context.beginPath();
	context.moveTo(sx(0), sy(0));
	for (var x = 0; x < 1400; x++) {
		drawIntermediate(x, walks, WALKS);
	}
	context.lineWidth=10;
    context.strokeStyle = '#ff0000';
	context.stroke();
});
</script>
</body>
</html>