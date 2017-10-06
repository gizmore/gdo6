<?php
use GDO\Perf\GDT_PerfBar;
$bar instanceof GDT_PerfBar;
$i = GDT_PerfBar::data();
printf('<span class="gdo-perf-bar">');
printf('|<i>%d&nbsp;Log</i>|<i>%d&nbsp;Qry</i>|<i>%d&nbsp;Wr</i>|<b>%d&nbsp;Tr</b> ', $i['logWrites'], $i['dbQueries'], $i['dbWrites'], $i['dbCommits']);
printf('|<i>%.03fs&nbsp;DB</i>+<i>%.03fs&nbsp;PHP</i>=<b>%.03fs</b>', $i['dbTime'], $i['phpTime'], $i['totalTime']);
printf('|<b>%.02f&nbsp;MB</b> ', $i['memory_max']/(1024*1024));
printf('|<i>%d&nbsp;class</i>|<i>%d&nbsp;gdo</i>|<i>%d&nbsp;mod</i>|<i>%d&nbsp;langfs</i> ', $i['phpClasses'], $i['gdoFiles'], $i['gdoModules'], $i['gdoLangFiles']);
printf('|<b>%d&nbsp;Tpl</b>|<b>%d&nbsp;Hooks</b>', $i['gdoTemplates'], $i['gdoHooks']);
printf('</span>');
