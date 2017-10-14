<?php
return array(
'sitename' => GWF_SITENAME,

'no_selection' => 'No selection',

# util/gdo and util/gwf fields
'id' => 'ID',
'file' => 'Datei',
'folder' => 'Ordner',
'title' => 'Titel',
'description' => 'Beschreibung',
'info' => 'Info',
'message' => 'Nachricht',
'captcha' => 'Captcha',
'user' => 'Benutzer',
'ip' => 'IP',
'username' => 'Username',
'gender' => 'Geschlecht',
'email' => 'E-Mail',
'email_fmt' => 'E-Mail Format',
'language' => 'Sprache',
'country' => 'Land',
'password' => 'Passwort',
'user_real_name' => 'Realname',
'name' => 'Name',
'enabled' => 'Aktiv',
'sort' => 'Sortierung',
'version' => 'Version',
'path' => 'Pfad',
'retype' => 'Erneut eingeben',
'user_allow_email' => 'Erlaube Nutzern mir eine E-Mail zu senden',
'birthdate' => 'Birthdate',
'url' => 'URL',
'guest' => '~~Guest~~',
'size' => 'Größe',
'type' => 'Typ',
'level' => 'Level',
'count' => 'Anzahl',
'credits' => 'Credits',
'permission' => 'Berechtigung',
'created_at' => 'Erstellt am',
'created_by' => 'Erstellt vom',

# Generic Buttons
'btn_back' => 'Zurück',
'btn_send' => 'Senden',
'btn_save' => 'Speichern',
'btn_upload' => 'Hochladen',

# Generic Messages
'msg_form_saved' => 'Ihre Daten wurden erfolgreich gespeichert.',
'msg_upgrading' => 'Modul %s Version %s wird installiert.',
'msg_redirect' => 'Sie werden in %2$s Sekunden weitergeleitet nach <i>%1$s</i>.',

# Generic Errors
'err_db' => "Databank Fehler: %s<br/>\n%s<br/>\n",
'err_token' => 'Ihr Token ist ungültig.',
'err_csrf' => 'Ihr Formular-Token ist ungültig. Haben Sie versucht ein Formular zweimal abzusenden? Eventuell haben Sie auch Cookies deaktiviert.',
'err_field_invalid' => 'Ihr Wert für %s wurde nicht akzeptiert.',
'err_blank_response' => 'Das Modul lieferte ein leeres Ergnis. Dies ist ungewöhnlich.',
'err_checkbox_required' => 'Sie müssen dieses Feld wählen, um fortzufahren.',
'err_strlen_between' => 'Dieses Feld muss zwischen %s und %s Zeichen lang sein.',
'err_form_invalid' => 'Ihr Formular ist ungültig da es noch Fehler enthält.',
'err_user_required' => 'Für diese Funktion müssen Sie angemeldet sein. Sie können sich auch <a href="%s" title="Nickname wählen">als Gast anmelden.',
'err_upload_min_files' => 'Sie müssen mindestens %s Datei(en) hochladen.',
'err_upload_max_files' => 'Sie dürfen nicht mehr als %s Datei(en) hochladen.',
'err_permission_required' => 'Sie brauchen die <i>%s</i> Berechtigung um auf diese Funktion zuzugreifen.',
'err_save_unpersisted_entity' => 'Tried to save an unpersisted entity of type <i>%s</i>.',
'err_file' => 'Datei nicht gefunden: %s',
'err_already_authenticated' => 'Sie sind bereits angemeldet.',
'err_gdo_not_found' => 'Zeile %s mit ID %s konnte nicht gefunden werden.',
'err_string_pattern' => 'Ihre Eingabe stimmt nicht mit der Eingabemaske überein.',
'err_url_not_reachable' => 'Ihre URL ist nicht erreichbar von diesem Server.',
'err_method_disabled' => 'Diese Funktion ist zur Zeit deaktiviert.',
'err_not_null' => 'Dieses Feld darf nicht leer sein.',
'err_user_type' => 'Ihr Konto ist nicht vom Typ %s.',
'err_table_not_sortable' => 'Die %s Tabelle kann nicht ohne GDT_Sort Feld sortiert werden.',
'err_pass_too_short' => 'Ihr Passwort muss mindestens %s Zeichen lang sein.',
'err_module_method' => 'Die Funktion konnte nicht gefunden werden.',
'err_invalid_choice' => 'Ihre Auswahl ist ungültig.',
'err_permission_create' => 'Sie dürfen keine Zeile dieses Typs anlegen.',
'err_permission_update' => 'Sie dürfen diese Objekt nicht bearbeiten.',
'err_path_not_exists' => 'Der angegebene Pfad istg kein %s: %s',
'err_int_not_between' => 'Dieses Feld muss zwischen %s und %s sein.',
'err_min_date' => 'Das Datum muss nach %s sein.',
'err_max_date' => 'Das Datum muss bevor %s sein.',
'err_db_unique' => 'Dieser Eintrag existiert bereits.',
'err_image_format_not_supported' => 'Das Bildformat %s wird nicht unterstützt.',
	
# Permissions
'perm_admin' => 'Administrator',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Staff',

# Dateformats
'df_day' => 'd.m.Y',
'df_short' => 'd.m.Y H:i',
'tu_s' => 's',
'tu_m' => 'm',
'tu_h' => 'h',
'tu_d' => 'd',
'tu_y' => 'y',

# Files
'filesize' => array('B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB', 'ZB'),

# Email formats
'enum_html' => 'HTML',
'enum_text' => 'Text',

# Gender
'enum_male' => 'männlich',
'enum_female' => 'weiblich',
'enum_no_gender' => 'keine Angabe',

# CRUD
'ft_crud_create' => '[%s] Erstelle %s',
'ft_crud_update' => '[%s] Bearbeite %s',
'msg_crud_created' => '%s wurde erstellt.',
'msg_crud_updated' => '%s wurde bearbeitet.',
'msg_crud_deleted' => '%s wurde gelöscht.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Application',
'sidenav_right_title' => 'Settings',
    
# Config
'ipp' => 'Items per page (ipp)',
'minify_js' => 'Minify Javascript On-the-fly?',
'enum_no' => 'No',
'enum_yes' => 'Yes',
'enum_concat' => 'Concatenate',
################################################
'link_node_detect' => 'Detect uglify binaries…',
'msg_nodejs_detected' => 'The nodejs binary has been detected: <i>%s</i>',
'msg_annotate_detected' => 'The ng-annotate binary has been detected: <i>%s</i>',
'msg_uglify_detected' => 'The uglify-js binary has been detected: <i>%s</i>',
    
);
