"use strict";
/**
 * Javascript error handler 
 */
window.GDO = window.GDO||{};

GDO.shortDebugURL = function(url) {
	let pattern = '(' + GDO_PROTOCOL + "://" + GDO_DOMAIN + GDO_WEB_ROOT;
	pattern += '([^? ]+)[ ?$][^ ]*)';
	pattern = new RegExp(pattern);
	return url.replace(pattern, '$2');
};

window.onerror = function (msg, url, lineNo, columnNo, error) {
	let message = msg + ' in ' + GDO.shortDebugURL(url) + ' line ' + lineNo + " column " + columnNo;
	let data = {
		url: location.href + "?" + location.search + '#' + location.hash,
		message: message,
		stack: GDO.shortDebugURL(error.stack),
	};
	window.GDO.xhr(GDO_WEB_ROOT + 'index.php?mo=Javascript&me=Error', 'POST', data);
	window.GDO.error(message, 'Error');
	return false;
};
