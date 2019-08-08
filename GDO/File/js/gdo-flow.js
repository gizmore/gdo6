document.querySelectorAll('.gdo-flow-file').forEach(function(input){
	var flow = new Flow({
		target: location.href + "&ajax=1&fmt=json&flowField="+input.name,
		withCredentials: true,
		fileParameterName: input.name,
		singleFile: input.className.indexOf('multiple') >= 0,
		testChunks: false,
	});
	
	flow.assignBrowse(input);
	
	flow.on('fileAdded', function(file, event){
		setTimeout(flow.upload.bind(flow), 200);
	});
	flow.on('fileSuccess', function(file,message){
		console.log(input.name);
		console.log(file);
		console.log(message);
		var preview = document.getElementById('gdo-file-preview-'+input.name);
		if (!preview) {
			console.error('Cannot find gdo-file-preview-'+input.name);
			return;
		}

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
	});
	flow.on('fileError', function(file, message){
		message = JSON.parse(message);
		alert(message.data.error);
	});
	
});
