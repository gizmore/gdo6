"use strict";

/**
 * Let'z'eh'goh
 */
window.GDO = {};

/**
 * Automatically focus the first editable form field.
 */
window.GDO.autofocusForm = function() {
	var id = window.GDO_FIRST_EDITABLE_FIELD;
	if (id) {
		var e = window.document.getElementById(id);
		e && e.focus();
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

window.GDO.error = function(response) {
	if (response.data && response.data.error && response.data.error.error) {
		var message = response.data.error;
	}
	else if (response.error) {
		var message = response.error;
	}
	alert(message);
};

window.GDO.openDialog = function(dialogId) {
	var dlg = document.querySelector('#'+dialogId+' dialog');
	if (!dlg) {
		console.error('Cannot find dialog with id ' + dialogId)
	}
	dlg.showModal();
}

window.GDO.href = function(module, method, append) {
	return GWF_WEB_ROOT + 'index.php?mo=' + module + '&me=' + method + append;
}
