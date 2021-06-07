<?php
return array(
# Welcome
'install_title_1' => 'Welcome',
'install_text_1' => 'Welcome to GDO6, Please continue here: %s',
'install_text_2' => 'If you plan to use a Database, please execute the following mysql commands.',
    
# System Test
'install_title_2' => 'System–Test',
'install_title_2_tests' => 'Mandatory Requirements',
'install_test_0' => 'Is PHP Version '.PHP_VERSION.'supported?',
'install_test_1' => 'Is the protected folder writable?',
'install_test_2' => 'Is the files folder writable?',
'install_test_3' => 'Is the temp folder writable?',
'install_test_4' => 'Is the assets folder writable?',
'install_test_5' => 'Are nodejs, npm, bower and yarn available?',
'install_test_6' => 'Is PHP mbstring installed?',
'install_test_7' => 'Is a timezone set in php.ini?',
'install_test_8' => 'Is fileinfo extension available?',
'install_title_2_optionals' => 'Optional Features',
'install_optional_0' => 'Is PHP gd installed?',
'install_optional_1' => 'Is PHP memcached installed?',
'install_system_ok' => 'Your system is able to run GDO6. You can continue with %s.',
'install_system_not_ok' => 'Your system is currently not able to run GDO6. You can try again to run %s.',

# Config
'install_title_3' => 'GDO Configuration',
'ft_install_configure' => 'Write configuration file',
'install_config_section_site' => 'Site',
'cfg_sitename' => 'Short Sitename',
'language' => 'Main Language',
'timezone' => 'Timezone',
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
'save_config' => 'Save',
'test_config' => 'Test',
    
# Modules
'install_title_4' => 'GDO Modules',
'install_modules_info_text' => 'Here you can choose the modules to install. Dependencies are not 100% resolved yet.',
'install_modules_completed' => 'Your modules have been installed. You can continue with %s',
'err_disable_core_module' => 'You cannot disable a core module.',
'err_multiple_site_modules' => 'You should not have multiple site modules.',
'err_missing_dependency' => 'You are missing dependencies: ',
'module_priority' => 'Priority',
'module_description' => 'Description',

# Cronjob
'install_title_5' => 'Cronjob Configuration',
'install_cronjob_info' => '
You should create a cronjob on your server.
You can paste this into your crontab file:

%s

You can then continue with %s.',

# Admins 
'install_title_6' => 'Create Admins',
'ft_install_installadmins' => 'Create Admins',
'msg_admin_created' => 'An admin named %s has been created or their password has been reset.',

'install_title_7' => 'Install Javascript',
'install_content_7' => '
<p>You should now install node,npm,bower,yarn and other javascript components.</p>
<p>Alternatively you have to upload these dependencies individually.</p>
<p>Run the following commands on your debian machine:<p>
<code>
As Root:<br/>
<br/>
aptitude install nodejs nodejs-dev npm # Install javascript<br/>
npm install -g bower # Install bower<br/>
npm install -g yarn # Install yarn<br/>
<br/>
As gdo6 user:<br/>
<br/>
cd www/gdo6<br/>
./gdo_bower.sh # Install module js dependencies<br/>
./gdo_yarn.sh # Install module js dependencies<br/>
<br/>
Note: Currently bower and yarn are both in use. Bower will be dropped.<br/>
</code>
',
	
'install_title_8' => 'Import Backup',
'ft_install_importbackup' => 'Import a backup',
	
'install_title_9' => 'Copy htaccess (optional)',
'ft_install_copyhtaccess' => 'Copy default htaccess to gdo6 root',
'copy_htaccess_info' => '<b>This overwrites a currently present <i>.htaccess</i> file!</b><br>You can then continue with %s.',
'copy_htaccess' => 'Copy default htaccess',

'install_title_10' => 'Security',
'ft_install_security' => 'Finish installation by removing access to install wizard and the protected folder',
'protect_folders' => 'Protect Folders',
	
# gdo6 binary
'msg_config_written' => 'A default config file has been written to protected/%s',
'msg_available_config' => 'Available config vars for module %s: %s.',
'msg_set_config' => 'The config var for %s in module %s is currently set to %s.',
'msg_changed_config' => 'The config var for %s in module %s has been set from %s to %s.',
'msg_installing_modules' => 'Installing the following dependant modules: %s.',

# 6.10
'bot_mail' => 'Bot Email',
'bot_name' => 'Bot Name',
'admin_mail' => 'Administrator Email',
'error_mail' => 'Error Email',
'msg_install_security' => 'Your gdo6 installation is now secured for the world wide web.',
);
