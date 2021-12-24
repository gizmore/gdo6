<?php
return [
    'cfg_minify_js' => 'Javascript minify mode',
    'cfg_compress_js' => 'Javascript minify compression',
    'cfg_nodejs_path' => 'Path to nodejs',
    'cfg_uglifyjs_path' => 'Path to uglify-js',
    'cfg_ng_annotate_path' => 'Path to ng-annotate',
    'cfg_link_node_detect' => 'Search Javascript binaries',
    'link_node_detect' => 'Detect javascript binaries…',
    'msg_nodejs_detected' => 'The nodejs binary has been detected: <i>%s</i>',
    'msg_annotate_detected' => 'The ng-annotate binary has been detected: <i>%s</i>',
    'msg_uglify_detected' => 'The uglify-js binary has been detected: <i>%s</i>',
    'err_nodejs_not_found' => 'Could not find nodejs',
    'err_annotate_not_found' => 'Could not find ng-annotate',
    'err_uglify_not_found' => 'Could not find uglify-js',
    'enum_concat' => 'Minify and merge',
    'info_detect_node_js' => 'Detect installed js binaries.<br/>
Install them <em>before</em> you run this method.<br/>
<br/>
apt-get install nodejs<br/>
npm install uglify-js -g<br/>
npm install -g ng-annotate-patched<br/>',
	'mailb_js_error' => '
A Javascript error occured:<br/>
<br/>
URL: %s<br/>
<br/>
Message: %s<br/>
-----------------------
<pre>
%s</pre><br/>
<br/>
Kind Regards<br/>
The %s system<br/>
',
];
