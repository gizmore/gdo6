<?php
namespace GDO\Date\lang;
return [
	'gdo_timezone' => 'fuso orario',
	'ago' => '%s fa',
    'err_min_date' => 'Questa data non deve essere %s.',
    'err_max_date' => 'Questa data deve essere prima del %s.',
    'err_invalid_date' => 'Invalid Time: %s does not match the format of %s. Please make sure to setup your language and timezone.',

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
    'ft_date_timezone' => 'Imposta il tuo fuso orario',
    'msg_timezone_changed' => 'Il tuo fuso orario ora è %s.',
    'cfg_tz_probe_js' => 'Determina il fuso orario con Javascript?',
    'cfg_tz_sidebar_select' => 'Mostra la selezione del fuso orario nella barra laterale?',
 
    # Epoch
    'mtitle_date_epoch' => 'Data e ora di uscita',
    'msg_time_unix' => 'Unix timestamp: %s',
    'msg_time_java' => 'Java timestamp: %s',
    'msg_time_micro' => 'Microtimestamp: %s',

	# Duration
	'duration' => 'Durata',
	'err_min_duration' => 'La durata deve essere almeno % s secondi.',
	
];
