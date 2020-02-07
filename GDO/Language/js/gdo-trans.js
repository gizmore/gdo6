"use strict";
function GDO_Trans() {
	
	this.CACHE = {};
	
	this.t = function(key, ...args) {
		key = this.CACHE[key] ? this.CACHE[key] : key;
		return vsprintf(key, args);
	};

}

window.GDO_TRANS = new GDO_Trans();
window.t = window.GDO_TRANS.t.bind(window.GDO_TRANS);
