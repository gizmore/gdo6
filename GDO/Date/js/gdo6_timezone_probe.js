"use strict";

window.GDO.Date = {

	probe: function() {
		try {
			var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
			var url = '?mo=Date&me=Timezone&_ajax=1&tzform[submit]=1&tzform[timezone]='+tz;
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
	if (window.GDO_USER.JSON.user_timezone === 'UTC') {
		window.GDO.Date.probe();
	}
});

