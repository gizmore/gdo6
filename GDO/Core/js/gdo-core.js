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

/**
 * Init GDO612js
 * @returns interest
 */
document.addEventListener('DOMContentLoaded', function(){
	
	window.GDO.autofocusForm();

}, false);
