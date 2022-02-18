"use strict";
function GDO_Trans() {
	
	this.CACHE = {};
	
	this.t = function(key) {
		try {
			key = this.CACHE[key] || key;
			if (key instanceof Array) {
				return key;
			}
			var args = Array.prototype.slice.call(arguments);
			args.shift();
			return vsprintf(key, args);
		}
		catch (ex) {
			console.error(ex);
			return key;
		}
	};
}
if (window.GDO_TRANS) {
	var cache = window.GDO_TRANS.CACHE;
}
window.GDO_TRANS = new GDO_Trans();
if (cache) {
	window.GDO_TRANS.CACHE = cache;
}
window.t = window.GDO_TRANS.t.bind(window.GDO_TRANS);
