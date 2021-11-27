"use strict";

window.GDO.Date = {
		
	probe: function() {
		try {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			var url = GDO_WEB_ROOT + 'index.php?mo=Date&me=TimezoneDetect&_ajax=1&tzform[submit]=1&tzform[timezone]='+tz;
			var req = new XMLHttpRequest();
			req.addEventListener("load", function(response) {
				console.log(response);
			});
			req.open("POST", url);
			req.send();
		}
		catch (e) {
			window.GDO.exception(e);
		}
	}

};

document.addEventListener("DOMContentLoaded", function(event) {
	console.log(window.GDO_USER);
	if (window.GDO_USER.JSON.user_timezone == 1) {
		window.GDO.Date.probe();
	}
});

