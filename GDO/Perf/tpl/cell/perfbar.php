<?php
use GDO\Perf\GDT_PerfBar;
/** @var $bar GDT_PerfBar **/
$i = GDT_PerfBar::data();
printf('<span class="gdo-perf-bar">');
// printf('Page performance');
printf('<i>%d&nbsp;Log</i>|<i>%d&nbsp;Qry</i>|<i>%d&nbsp;Wr</i>|<b>%d&nbsp;Tr</b> ', $i['logWrites'], $i['dbQueries'], $i['dbWrites'], $i['dbCommits']);
printf('|<i>%.03fs&nbsp;DB</i>+<i>%.03fs&nbsp;PHP</i>=<b>%.03fs</b>', $i['dbTime'], $i['phpTime'], $i['totalTime']);
printf('|<b>%.02f&nbsp;MB</b> ', $i['memory_max']/(1024*1024));
printf('|<i>%d&nbsp;Classes</i>|<i>%d&nbsp;gdoClasses</i>|<b><i>%d&nbsp;GDT</i></b>|<b><i>%d&nbsp;GDO</i></b>|<i>%d&nbsp;mod</i>|<i>%d&nbsp;langfs</i> ', $i['phpClasses'], $i['gdoFiles'], $i['gdtCount'], $i['gdoCount'], $i['gdoModules'], $i['gdoLangFiles']);
printf('|<b>%d&nbsp;tmpl</b>|<b title="Hooks">%d&nbsp;hook</b>|<b>%d&nbsp;ipc</b>|<b>%d&nbsp;mail</b>', $i['gdoTemplates']/*, implode(',', $i['gdoHookNames'])*/, $i['gdoHooks'], $i['gdoIPC'], $i['gdoMails']);
printf('</span>');
