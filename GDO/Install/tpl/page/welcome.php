<?php
use GDO\Install\Config;
?>
<h2><?= t('install_title_1') ?></h2>

<p><?= t('install_text_1', [Config::linkStep(2)]); ?></p>

<p><?= t('install_text_2'); ?></p>

<pre>
CREATE DATABASE gdo6;
GRANT ALL ON gdo6.* TO gdo6@localhost IDENTIFIED BY 'gdo6';
</pre>
