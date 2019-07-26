<input
 type="checkbox"
 id="toggle_all"
 checked="checked"
 onclick="toggleAll(this)" />

<script type="text/javascript">
function toggleAll(cbxAll) {
	var checkboxes = document.querySelectorAll('.gdo-module-install-cbx');
	for (var i in checkboxes) {
		var cbx = checkboxes[i];
		cbx.checked = cbxAll.checked;
	}
	if (!cbxAll.checked) {
		enableCoreModules();
	}
}
</script>
