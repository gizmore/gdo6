"use strict";

/**
 * Let'z'eh'goh
 */
window.GDO = {};

/**
 * Automatically focus the first editable form field.
 */
window.GDO.autofocusForm = function() {
	var id = window.GDO_FIRST_EDITABLE_FIELD, e;
	if (id) {
		if (e = window.document.getElementById(id)) {
			e.focus();
		}
	}
};

window.GDO.error = function(html, title) {
	alert(html);
};

/**
 * Init GDO612js
 * @returns interest
 */
document.addEventListener('DOMContentLoaded', function(){
	
	window.GDO.autofocusForm();

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
		var message = response.data.error.error;
	}
	else if (response.error) {
		var message = response.error;
	}
	alert(message);
};
