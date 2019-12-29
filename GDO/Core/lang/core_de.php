<?php
return array(
'sitename' => GWF_SITENAME,

'enum_none' => 'Nichts',
'no_selection' => 'Keine Auswahl',

# util/gdo and util/gwf fields
'birthdate' => 'Geburtsdatum',
'captcha' => 'Captcha',
'color' => 'Farbe',
'count' => 'Anzahl',
'country' => 'Land',
'created_at' => 'Erstellt am',
'created_by' => 'Erstellt von',
'deleted_at' => 'Gelöscht am',
'deleted_by' => 'Gelöscht von',
'credits' => 'Kredite',
'description' => 'Beschreibung',
'email' => 'E-Mail',
'email_fmt' => 'E-Mail Format',
'enabled' => 'Aktiv',
'file' => 'Datei',
'folder' => 'Ordner',
'gender' => 'Geschlecht',
'guest' => '~~Gast~~',
'id' => 'ID',
'info' => 'Info',
'ip' => 'IP',
'language' => 'Sprache',
'level' => 'Level',
'message' => 'Nachricht',
'name' => 'Name',
'password' => 'Passwort',
'path' => 'Pfad',
'permission' => 'Berechtigung',
'priority' => 'Priorität',
'retype' => 'Erneut eingeben',
'search' => 'Suchen',
'size' => 'Größe',
'sort' => 'Sort.',
'title' => 'Titel',
'tooltip' => 'Hilfetext',
'type' => 'Typ',
'url' => 'URL',
'user' => 'Benutzer',
'username' => 'Benutzername',
'user_real_name' => 'Realname',
'version' => 'Version',
'not_specified' => 'keine Angabe',
'no_match' => 'Kein Treffer',
'view' => 'Ansehen',
	
# Generic Buttons
'btn_back' => 'Zurück',
'btn_set' => 'Setzen',
'btn_send' => 'Senden',
'submit' => 'Senden',
'btn_save' => 'Speichern',
'btn_upload' => 'Hochladen',
'btn_edit' => 'Bearbeiten',
'btn_delete' => 'Löschen',
'btn_view' => 'Ansehen',
'btn_preview' => 'Vorschau',
'btn_visible' => 'Sichtbar schalten',
'btn_invisible' => 'Unsichtbar schalten',
'btn_send_mail' => 'Email senden',
'btn_confirm' => 'Bestätigen',
'btn_cancel' => 'Abbrechen',
'link_overview' => 'Übersicht',
	
# Generic Messages
'msg_form_saved' => 'Ihre Daten wurden erfolgreich gespeichert.',
'msg_upgrading' => 'Installiere Modul %s Version %s.',
'msg_redirect' => 'Sie werden in %2$s Sekunden umgeleitet nach <i>%1$s</i>.',

# Generic Confirms
'confirm_delete' => 'Möchten Sie das wirklich löschen?',
	
# Generic Errors
'err_db' => "Datenbank Fehler(%s): %s<br/>\n%s<br/>\n",
'err_user' => 'Dieser Benutzer ist unbekannt.',
'err_exception' => 'Ausnahme: %s',
'err_gdo_object_no_table' => '%s ist ein GDT_Object ohne Tabelle.',
'err_column' => 'Unbekannte GDT Spalte: %s.',
'err_token' => 'Ihr Token ist ungültig.',
'err_csrf' => 'Ihr Formulartoken ist ungültig. Vielleicht haben Sie das Formular erneut abgesendet oder Sie haben Cookie-Probleme',
'err_field_invalid' => 'Ihr Wert für %s wurde nicht akzeptiert.',
'err_blank_response' => 'Das Modul gab eine leere Antwort, was ungewöhnlich ist.',
'err_checkbox_required' => 'Sie müssen dieses Feld anwählen um fortzufahren.',
'err_strlen_between' => 'Dieser Text muss zwischen %s und %s Zeichen lang sein.',
'err_form_invalid' => 'Ihr Formular ist unvollständig und enthält Fehler.',
'err_user_required' => 'Für diese Funktion müssen Sie angemeldet sein. Es ist auch möglich sich <a href="%s" title="Nickname wählen">als Gast anzumelden</a>.',
'err_upload_min_files' => 'Sie müssen mehr als %s Datei(en) hochladen.',
'err_upload_max_files' => 'Sie dürfen nicht mehr als %s Datei(en) hochladen.',
'err_create_dir' => 'Verzeichnis konnte nicht erstellt werden: %s.',
'err_permission_required' => 'Sie benötigen die <i>%s</i> Berechtigung um diese Funktion aufzurufen.',
'err_save_unpersisted_entity' => 'Es wurde versucht eine unpersitierte Entity des Typs <i>%s</i> zu speichern.',
'err_file_not_found' => 'Datei nicht gefunden: %s',
'err_already_authenticated' => 'Sie sind bereits angemeldet.',
'err_gdo_not_found' => 'Konnte %s mit der ID %s nicht finden.',
'err_string_pattern' => 'Ihre Eingabe passt nicht zur Eingabemaske.',
'err_url_not_reachable' => 'Ihre eingegebene URL kann vom Server nicht erreicht werden.',
'err_method_disabled' => 'Diese Funktion ist deaktiviert',
'err_not_null' => 'Dieses Feld darf nicht leer sein.',
'err_user_type' => 'Ihr Konto ist nicht vom Typ %s.',
'err_table_not_sortable' => 'Die %s Tabelle kann nicht ohne ein GDT_Sort sortiert werden.',
'err_pass_too_short' => 'Ihr Passwort muss mindestens %s Zeichen lang sein.',
'err_module_method' => 'Die Funktion konnte nicht gefunden werden.',
'err_invalid_choice' => 'Ihre Auswahl ist ungültig.',
'err_permission_create' => 'Sie dürfen das Objekt nicht anlegen.',
'err_permission_update' => 'Sie dürfen dieses Objekt nicht bearbeiten.',
'err_permission_delete' => 'Sie dürfen dieses Objekt nicht löschen.',
'err_path_not_exists' => 'Der angegebene Pfad ist kein %s: %s',
'err_int_not_between' => 'Diese Zahl muss zwischen %s und %s betragen.',
'err_min_date' => 'Dieses Datum muss nach %s sein.',
'err_max_date' => 'Dieses Datum muss vor %s sein.',
'err_db_connect' => 'Datenbankverbindung gescheitert.',
'err_db_unique' => 'Dieser Eintrag existiert bereits.',
'err_image_format_not_supported' => 'Das Bildformat wird nicht unterstützt: %s',
'err_members_only' => 'Diese Funktion steht nur Mitgliedern zur Verfügung.',
'err_session_required' => 'Ihre Sitzung ist abgelaufen.',
'err_not_allowed' => 'Ihnen fehlt die Berechtigung: %s.',

# Permissions
'sel_no_permissions' => 'Keine Berechtigung nötig',
'perm_admin' => 'Administrator',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Mitarbeiter',
	
# User types
'enum_ghost' => 'Geist',
'enum_guest' => 'Gast',
'enum_system' => 'System',
'enum_member' => 'Mitglied',

# Dateformats
'df_day' => '%d.%m.%Y',
'df_short' => '%d.%m.%Y %H:%M',
'tu_s' => 's',
'tu_m' => 'm',
'tu_h' => 'h',
'tu_d' => 'd',
'tu_y' => 'y',

# Checkbox
'enum_undetermined_yes_no' => 'unbekannt',
'enum_unknown' => 'Unbekannt',
	
# Files
'image' => 'Bild',
'icon' => 'Icon',
	
# Email formats
'enum_html' => 'HTML',
'enum_text' => 'Text',

# Gender
'enum_male' => 'männlich',
'enum_female' => 'weiblich',
'enum_no_gender' => 'keine Angabe',

# CRUD
'ft_crud_create' => '%s erstellen',
'ft_crud_update' => '%s bearbeiten',
'msg_crud_created' => '%s wurde erfolgreich erstellt.',
'msg_crud_updated' => '%s wurde erfolgreich bearbeitet.',
'msg_crud_deleted' => '%s wurde erfolgreich gelöscht.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Anwendung',
'sidenav_right_title' => 'Einstellungen',
	
# Config
'ipp' => 'Einträge pro Seite',
'minify_js' => 'Javascript optimieren?',
'enum_no' => 'Nein',
'enum_yes' => 'Ja',
'enum_concat' => 'auch zusammenführen',
	
# Welcome
'core_welcome_box_info' => 'Willkommen auf %s. Sie sind angemeldet als %s.',
'link_impressum' => 'Impressum',

################################################
'link_node_detect' => 'uglify Programm suchen…',
'msg_nodejs_detected' => 'Das nodejs Programm wurde gefunden: <i>%s</i>',
'msg_annotate_detected' => 'Das ng-annotate Programm wurde gefunden: <i>%s</i>',
'msg_uglify_detected' => 'Das uglify-js Programm wurde gefunden: <i>%s</i>',
#
'quote_by' => 'Zitat von %s',
'quote_at' => 'am %s',

	
# Filter
'int_filter' => 'Num',
'string_filter' => 'Text',
'object_filter' => 'Filter',
'sel_all' => 'Alle',
'sel_checked' => 'Aktiv',
'sel_unchecked' => 'Inaktiv',
	
);
