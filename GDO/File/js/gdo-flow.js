"use strict"
document.querySelectorAll('.gdo-flow-file input[type=file], input[type=file].gdo-flow-file').forEach(function(input){
	
	var flow = new Flow({
		target: location.href + "&ajax=1&fmt=json&flowField="+input.name,
		withCredentials: true,
		fileParameterName: input.name,
		singleFile: input.className.indexOf('multiple') < 0,
		testChunks: false,
	});
	
	flow.assignBrowse(input);
	
	flow.on('fileAdded', function(file, event){
		setTimeout(flow.upload.bind(flow), 200);
	});
	flow.on('fileSuccess', function(file,message){
		
//		console.log(input.name);
//		console.log(file);
//		console.log(message);
		
		var preview = document.getElementById('gdo-file-preview-'+input.name);

		if (!preview) {
			console.error('Cannot find gdo-file-preview-'+input.name);
			return;
		}
		
		if (file.file.type && file.file.type.startsWith('image/')) {
			if (document.querySelector('#gdo-file-preview-'+input.name)) {
				var div = document.createElement("DIV");
				div.className = 'gdo-file-preview';
				var node = document.createElement("IMG");
				node.src = '#';
				div.appendChild(node);
				preview.appendChild(div); 
				var reader = new FileReader();
				reader.onload = function(e) {
					node.src = e.target.result;
				}
				reader.readAsDataURL(file.file);
			}
		}

		var previewString = document.getElementById('gdo-file-input-'+input.name);
		var text = file.file.name + " (" + file.file.size + " bytes)";
		try {
			previewString.value = text;
		} catch (e) {
			previewString.innerHTML = text;
		}

	});
	flow.on('fileError', function(file, message){
		message = JSON.parse(message);
		alert(message.data.error);
	});
	
});
