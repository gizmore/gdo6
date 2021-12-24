<?php
namespace GDO\Date\lang;
return [
	'gdo_timezone' => 'Zeitzone',
	'ago' => 'vor %s',
    'err_min_date' => 'Dieses Datum muss nach %s sein.',
    'err_max_date' => 'Dieses Datum muss vor %s sein.',
    'err_invalid_date' => 'Ungültige Zeitangabe: %s ist nicht im Format %s. Bitte stellen Sie sicher, dass Ihre Sprache und Zeitzone korrekt eingestellt sind.',

    # Dateformats
    'df_db' => 'Y-m-d H:i:s.v', # do not change
	'df_local' => 'Y-m-d\TH:i', # do not change
    'df_parse' => 'd.m.Y H:i:s.u',
    'df_ms' => 'd.m.Y H:i:s.v',
    'df_long' => 'd.m.Y H:i:s',
    'df_short' => 'd.m.Y H:i',
    'df_minute' => 'd.m.Y H:i',
    'df_day' => 'd.m.Y',
    'df_sec' => 'd.m.Y H:i:s',
    'tu_s' => 's',
    'tu_m' => 'm',
    'tu_h' => 'h',
    'tu_d' => 'd',
    'tu_w' => 'w',
    'tu_y' => 'y',
    
    # Timezone
    'ft_date_timezone' => 'Setzen Ihrer Zeitzone',
    'msg_timezone_changed' => 'Ihre Zeitzone ist nun %s.',
    'cfg_tz_probe_js' => 'Zeitzone mit Javascript ermitteln?',
    'cfg_tz_sidebar_select' => 'Zeitzohnenwahl in der Sidebar anzeigen?',

    # Epoch
    'mtitle_date_epoch' => 'Zeitstempel ausgeben',
    'msg_time_unix' => 'Unix timestamp: %s',
    'msg_time_java' => 'Java timestamp: %s',
    'msg_time_micro' => 'Microtimestamp: %s',
    
	# Duration
	'duration' => 'Dauer',
	'err_min_duration' => 'Die Dauer muss mindestens %s Sekunden betragen.',
];
