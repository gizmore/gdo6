"use strict";

window.GDO.Date = {
		
	probe: function() {
		var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
		var url = GDO_PROTOCOL + '://' + GDO_DOMAIN + GDO_WEB_ROOT + 'index.php?mo=Date&me=TimezoneDetect&_ajax=1&tzform[submit]=1&tzform[timezone]='+tz;
		var req = new XMLHttpRequest();
		req.addEventListener("load", function(response) {
//			console.log(response);
		});
		req.open("POST", url);
		req.send();
	},

};

document.addEventListener("DOMContentLoaded", function() {
	if (GDO_USER.JSON.user_timezone == 1) {
		GDO.Date.probe();
	}
});
