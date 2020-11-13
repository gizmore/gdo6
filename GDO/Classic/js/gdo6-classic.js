"use strict";

/**
 * Accordeon and Dialog.
 */
var c = document.querySelectorAll('.gdt-accordeon .collapse-bar');
var len = c.length;
for (var i = 0; i < len; i++) {
	c[i].onclick = function() {
		console.log('here');
		console.log(this);
		this.parentNode.classList.remove('closed');
		this.parentNode.classList.add('opened');
	};
}

c = document.querySelectorAll('.gdt-accordeon .uncollapse-bar');
var len = c.length;
for (var i = 0; i < len; i++) {
	c[i].onclick = function() {
		console.log('here2');
		console.log(this);
		this.parentNode.classList.remove('opened');
		this.parentNode.classList.add('closed');
	};
}

/** Dialog **/
