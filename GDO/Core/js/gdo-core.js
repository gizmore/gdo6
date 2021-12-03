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
	if (response.json && response.json.error) {
		var message = response.json.error;
	}
	else if (response.error) {
		var message = response.error;
	}
	
	if (response.json && response.json.stack) {
		message += "\n\n" + response.json.stack;
	}
	
	window.GDO.error(message, "Ajax Error");
};

window.GDO.error = function(html, title) {
	alert(title + "\n\n" + message);
};

window.GDO.exception = function(ex) {
	console.error(ex);
	return window.GDO.responseError({json:{error: ex.message, stack: ex.stack}});
};

window.GDO.OPEN_DIALOG = null;

window.GDO.closeDialog = function(resolve) {
	window.GDO.OPEN_DIALOG[0].close();
	window.GDO.OPEN_DIALOG[1].style.display = 'none';
	resolve();
}

window.GDO.openDialog = function(dialogId) {
	var dlg = document.querySelector('#'+dialogId+' dialog');
	if (!dlg) {
		console.error('Cannot find dialog with id ' + dialogId)
	}
	else {
		let wrap = document.querySelector('#'+dialogId);
		wrap.style.display = 'block';
		let promise = new Promise((reject, resolve) => {
			
		});
		GDO.OPEN_DIALOG = [dlg, wrap, promise];
		dlg.showModal();
		return promise;
	}
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
