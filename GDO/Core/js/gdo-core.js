"use strict";

/**
 * Let'z'eh'goh
 */
window.GDO = {};

/**
 * Automatically focus the first editable form field.
 */
window.GDO.autofocusForm = function() {
	let id = window.GDO_FIRST_EDITABLE_FIELD;
	if (id) {
		let e = window.document.getElementById(id);
		e && e.focus();
	}
};

window.GDO.enterForm = function(form, event) {
//	console.log('GDO.enterForm()', form, event);
	if (event.keyCode == 13) {
		let nn = event.srcElement.nodeName;
		if ( (nn === 'INPUT') || (nn === 'SELECT') ) {
			event.preventDefault();
			let submits = form.querySelectorAll('input[type=submit]');
			submits[0] && submits[0].click();
		}
	}
};

window.GDO.triggerResize = function() {
	setTimeout(
		GDO.triggerEvent.bind(window, 'resize')
		, 1000);
};

window.GDO.triggerEvent = function(name) {
	if (typeof(Event) === 'function') {
		window.dispatchEvent(new Event(name));
	}
	else {
		var evt = window.document.createEvent('UIEvents'); 
		evt.initUIEvent(name, true, false, window, 0); 
		window.dispatchEvent(evt);
	}
};

/**
 * Init GDO612js
 * @returns interest
 */
document.addEventListener('DOMContentLoaded', function(){
	setTimeout(window.GDO.autofocusForm, 1);
}, false);

window.GDO.toggleAll = function(toggler) {
	console.log(toggler);
	var tc = "."+toggler.getAttribute('gdo-toggle-class');
	console.log(tc);
	var cbxes = window.document.querySelectorAll(tc);
	cbxes.forEach(function(cbx){
		cbx.checked = toggler.checked;
	});
};

window.GDO.responseError = function(response) {
	let message = JSON.stringify(response);
	
	if ( (!response.json) && (response.responseJSON) ) {
		response.json = response.responseJSON.json;
	}
	
	if (response.json && response.json.error) {
		message = response.json.error;
	}
	else if (response.json && response.json.topResponse) {
		let r = response.json.topResponse;
		if (r.error) {
			message = r.error;
		} else {
			message = JSON.stringify(r);
		}
	}
	else if (response.error) {
		message = response.error;
	}
	
	if (response.json && response.json.stack) {
		message += "\n\n" + response.json.stack;
	}
	
	return window.GDO.error(message, "Error");
};

window.GDO.error = function(html, title) {
	alert(html);
//	alert(title + "\n\n" + html);
};

window.GDO.exception = function(ex) {
	console.error(ex);
	return window.GDO.responseError({json:{error: ex.message, stack: ex.stack}});
};

window.GDO.DIALOG_RESULT = null;

window.GDO.closeDialog = function(dialogId, result) {
	console.log('GDO.closeDialog()', dialogId, result);
	window.GDO.DIALOG_RESULT = result;
	let wrap = document.querySelector('#'+dialogId);
	let onclose = wrap.getAttribute('gdo-on-close');
	let dlg = document.querySelector('#'+dialogId+' dialog');
	dlg.close();
	wrap.style.display = 'none';
	if (onclose) {
		eval(onclose);
	}
}

window.GDO.openDialog = function(dialogId) {
	console.log('GDO.openDialog()', dialogId);
	var dlg = document.querySelector('#'+dialogId+' dialog');
	if (!dlg) {
		console.error('Cannot find dialog with id ' + dialogId);
		return;
	}
	let wrap = document.querySelector('#'+dialogId);
	wrap.style.display = 'block';
	dlg.showModal();
};

/**
 * @TODO make seo friendly urls like in PHP.
 * @see GDO6.php
 */
window.GDO.href = function(module, method, append) {
	return GDO_WEB_ROOT + 'index.php?mo=' + module + '&me=' + method + append;
};

/**
 * Inherit this class for GDO plugins.
 */
window.GDO.Plugin = function(config) {
	for (var i in config) {
		if (this[i] !== undefined) {
			this[i] = config[i];
		}
	}
};

window.GDO.xhr = function(url, method, data) {
	return fetch(url, {
		method: method||'GET',
		headers: {'Content-Type': 'application/json'},
		body: JSON.stringify(data)
	});
};

var origOpen = XMLHttpRequest.prototype.open;
XMLHttpRequest.prototype.open = function () {
	let result = origOpen.apply(this, arguments);
	let token = document.querySelector('meta[name="csrf-token"]');
	token = token ? token.getAttribute('content') : 'not-there';
	this.setRequestHeader('X-CSRF-TOKEN', token);
	this.withCredentials = true;
	return result;
};

/**
 * Get URL GET parameter.
 * @since 6.11.3
 */
window.GDO.GET = function(sParam, def) {
    let sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName;
    for (let i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return def;
};
