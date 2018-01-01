"use strict";
function GDO_Trans() {
	
	this.CACHE = {};
	
	this.init = function() {
		return $.get(sprintf('%s://%s%sindex.php?mo=Language&me=GetTranslationData&ajax=1&fmt=json', 
				window.GWF_PROTOCOL, window.GWF_DOMAIN, window.GWF_WEB_ROOT)).then(this.inited.bind(this));
	};
	
	this.inited = function(data) {
		this.CACHE = data;
	};
	
	this.t = function(key, ...args) {
		key = this.CACHE[key] ? this.CACHE[key] : key;
		
	};
	
}

window.GDO_TRANS = new GDO_Trans();
window.t = function() {};