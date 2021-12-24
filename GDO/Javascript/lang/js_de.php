<?php
return [
    'cfg_minify_js' => 'Javascript Minifizierungs-Modus',
    'cfg_compress_js' => 'Javascript Minifizierung Compression',
    'cfg_nodejs_path' => 'Pfad zu nodejs',
    'cfg_uglifyjs_path' => 'Pfad zu uglify-js',
    'cfg_ng_annotate_path' => 'Pfad zu ng-annotate',
    'cfg_link_node_detect' => 'Javascript Programme auf Server suchen',
    'link_node_detect' => 'Javascript Programme suchen',
    'msg_nodejs_detected' => 'Das nodejs Programm wurde gefunden: <i>%s</i>',
    'msg_annotate_detected' => 'Das ng-annotate Programm wurde gefunden: <i>%s</i>',
    'msg_uglify_detected' => 'Das uglify-js Programm wurde gefunden: <i>%s</i>',
    'err_nodejs_not_found' => 'Konnte nodejs nicht finden.',
    'err_annotate_not_found' => 'Konnte ng-annotate nicht finden.',
    'err_uglify_not_found' => 'Konnte uglify-js nicht finden.',
    'minify_js' => 'Javascript optimieren?',
    'enum_concat' => 'Minimieren und zusammenführen',
    'info_detect_node_js' => 'Javascript Programme suchen.<br/>
Installieren Sie diese <em>bevor</em> Sie dies ausführen.<br/>
<br/>
apt-get install nodejs<br/>
npm install uglify-js -g<br/>
npm install -g ng-annotate-patched<br/>',
	'mailb_js_error' => '
Ein Javascript Fehler ist aufgetreten:<br/>
<br/>
URL: %s<br/>
<br/>
Fehler: %s<br/>
-----------------------
<pre>
%s</pre><br/>
<br/>
Viele Grüße<br/>
Das %s System<br/>
',
];
