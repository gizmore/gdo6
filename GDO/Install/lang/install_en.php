<?php
return array(
# Welcome
'install_title_1' => 'Welcome',

# System Test
'install_title_2' => 'System–Test',
'install_test_0' => 'Is the protected folder writable?',
'install_test_1' => 'Is the files folder writable?',
'install_test_2' => 'Is the temp folder writable?',
'install_system_ok' => 'Your system is able to run GDO6. You can continue with %s.',
'install_system_not_ok' => 'Your system is currently not able to run GDO6. You can try again to run %s.',

# Config
'install_title_3' => 'GDO Configuration',
'ft_install_configure' => 'Write configuration file',
'install_config_section_site' => 'Site',
'cfg_sitename' => 'Short Sitename',
'language' => 'Main Language',
'themes' => 'Themes',
'install_config_section_http' => 'HTTP',
'install_config_section_files' => 'Files',
'enum_448' => '700',
'enum_504' => '770',
'enum_511' => '777',
'enum_en' => 'English',
'enum_de' => 'German',
'install_config_section_logging' => 'Logging',
'install_config_section_database' => 'Database',
'install_config_section_cache' => 'Cache',
'install_config_section_cookies' => 'Cookies',
'install_config_section_email' => 'Mail',
'err_db_connect' => 'The connection to the database server failed. Note that only mysql/maria is supported.',
'install_config_boxinfo_success' => 'Your system looks solid. You can continue with %s',
# Modules
'install_title_3' => 'GDO Modules',
'install_modules_info_text' => 'Here you can choose the modules to install. Dependencies are not resolved yet.',
'install_modules_completed' => 'Your modules have been installed. You can continue with %s',
# Cronjob
'install_title_4' => 'Cronjob Configuration',
'install_cronjob_info' => '
You should create a cronjob on your server.
You can paste this into your crontab file:

%s

You can then continue with %s.',

# Admins 
'install_title_5' => 'Create Admins',

'install_title_6' => 'Install Javascript',
'install_content_6' => '
<p>You should now install node,npm,bower and other javascript components.</p>
<p>Alternatively you have to upload these dependencies individually.</p>
<p>Run the following commands on your debian machine:<p>
<code>
As Root:<br/>
<br/>
aptitude install nodejs nodejs-dev npm # Install javascript<br/>
npm install -g bower # Install bower<br/>
<br/>
As gdo6 user:<br/>
<br/>
cd www/gdo6<br/>
./gdo_bower.sh # Install module js dependencies<br/>
</code>
',
    
    'install_title_7' => 'Install Security',
'ft_install_security' => 'Finish installation by removing access to install wizard and the protected folder',
    
);
