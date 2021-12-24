<?php
namespace GDO\Core\lang;
return array(
'sitename' => GDO_SITENAME,

'enum_none' => 'Nichts',
'enum_all' => 'Alle',
'enum_staff' => 'Mitarbeiter',
'no_selection' => 'Keine Auswahl',

# util/gdo and util/gwf fields
'all' => 'Alle',
'captcha' => 'Captcha',
'color' => 'Farbe',
'count' => 'Anzahl',
'country' => 'Land',
'created_at' => 'Erstellt am',
'created_by' => 'Erstellt von',
'edited_at' => 'Geändert am',
'edited_by' => 'Geändert von',
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
'guestname' => 'Gastname',
'user_real_name' => 'Realname',
'version' => 'Version',
'not_specified' => 'keine Angabe',
'no_match' => 'Kein Treffer',
'view' => 'Ansehen',
'page' => 'Seite',
'online' => 'Online',
'offline' => 'Offine',
'public' => 'Öffentlich',
'private' => 'Privat',
'admin' => 'Administration',
'timezone' => 'Zeitzone',
'filesize' => 'Dateigröße',
'font_weight' => 'Font-Stärke',
'perm_level' => 'Berechtigungslevel',
'menu' => 'Menü',
'download' => 'Herunterladen',
'order' => 'Reihenfolge',
'text' => 'Text',
'num' => 'Num',
'filter' => 'Filter',
'actions' => 'Aktionen',
'deleted' => 'Gelöscht',
'completed' => 'Erledigt',
'timezone' => 'Zeitzone',
'format' => 'Format',
    
# Core GDO tables
'gdo_permission' => 'Berechtigung',
    
# Generic Buttons
'btn_ok' => 'OK',
'btn_back' => 'Zurück',
'btn_set' => 'Setzen',
'btn_send' => 'Senden',
'submit' => 'Senden',
'btn_save' => 'Speichern',
'btn_upload' => 'Hochladen',
'btn_edit' => 'Bearbeiten',
'btn_create' => 'Erstellen',
'btn_approve' => 'Freischalten',
'btn_delete' => 'Löschen',
'btn_view' => 'Ansehen',
'btn_preview' => 'Vorschau',
'btn_visible' => 'Sichtbar schalten',
'btn_invisible' => 'Unsichtbar schalten',
'btn_send_mail' => 'Email senden',
'btn_confirm' => 'Bestätigen',
'btn_cancel' => 'Abbrechen',
'btn_overview' => 'Übersicht',
'btn_search' => 'Suchen',
'btn_sort' => 'Sortieren',
    
# Sorting
'order_by' => 'Reihenfolge',
'order_dir' => 'Richtung',
'list_order' => 'Reihenfolge: %s, %s',
'asc' => 'Aufsteigend',
'desc' => 'Absteigend',
'lbl_search_criteria' => 'Kriterien: %s',
    
# Generic Messages
'msg_form_saved' => 'Ihre Daten wurden erfolgreich gespeichert.',
'msg_upgrading' => 'Installiere Modul %s Version %s.',
'msg_redirect' => 'Sie werden in %2$s Sekunden umgeleitet nach <i>%1$s</i>.',

# Generic Confirms
'confirm_delete' => 'Möchten Sie das wirklich löschen?',
'iconfirm' => 'Ich bin sicher',
    
# Generic Errors
'err_db' => "Datenbank Fehler(%s): %s<br/>\n%s<br/>\n",
'err_user' => 'Dieser Benutzer ist unbekannt.',
'err_exception' => 'Ausnahme: %s',
'err_parameter_exception' => '%s: %s',
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
'err_upload_min_files' => 'Sie müssen mindestens %s Datei(en) hochladen.',
'err_upload_max_files' => 'Sie dürfen nicht mehr als %s Datei(en) hochladen.',
'err_create_dir' => 'Verzeichnis konnte nicht erstellt werden: %s.',
'err_permission_required' => 'Sie benötigen die <i>%s</i> Berechtigung um diese Funktion aufzurufen.',
'err_save_unpersisted_entity' => 'Es wurde versucht eine unpersitierte Entity des Typs <i>%s</i> zu speichern.',
'err_file_not_found' => 'Datei nicht gefunden: %s',
'err_already_authenticated' => 'Sie sind bereits angemeldet.',
'err_gdo_not_found' => 'Konnte %s mit der ID %s nicht finden.',
'err_no_data_yet' => 'Hier sind noch keine Daten vorhanden.',
'err_string_pattern' => 'Ihre Eingabe passt nicht zur Eingabemaske.',
'err_url_not_reachable' => 'Ihre eingegebene URL kann vom Server nicht erreicht werden.',
'err_method_disabled' => 'Diese Funktion ist deaktiviert',
'err_not_null' => 'Dieses Feld darf nicht leer sein.',
'err_user_type' => 'Das Konto ist nicht vom Typ %s.',
'err_table_not_sortable' => 'Die %s Tabelle kann nicht ohne ein GDT_Sort sortiert werden.',
'err_pass_too_short' => 'Ihr Passwort muss mindestens %s Zeichen lang sein.',
'err_module' => 'Das Modul konnte nicht gefunden werden: %s.',
'err_module_method' => 'Die Funktion konnte nicht gefunden werden.',
'err_invalid_choice' => 'Ihre Auswahl ist ungültig.',
'err_permission_create_level' => 'Sie benötigen einen Nutzerlevel von %s um dies anzulegen.',
'err_permission_create' => 'Sie dürfen das Objekt nicht anlegen.',
'err_permission_read' => 'Sie dürfen diesen Eintrag nicht sehen.',
'err_permission_update' => 'Sie dürfen dieses Objekt nicht bearbeiten.',
'err_permission_delete' => 'Sie dürfen dieses Objekt nicht löschen.',
'err_path_not_exists' => 'Der angegebene Pfad ist %2$s: %1$s',
'err_int_not_between' => 'Diese Zahl muss zwischen %s und %s betragen.',
'err_db_connect' => 'Datenbankverbindung gescheitert.',
'err_db_unique' => 'Dieser Eintrag existiert bereits.',
'err_image_format_not_supported' => 'Das Bildformat wird nicht unterstützt: %s',
'err_members_only' => 'Diese Funktion steht nur Mitgliedern zur Verfügung.',
'err_session_required' => 'Ihre Sitzung ist abgelaufen.',
'err_not_allowed' => 'Ihnen fehlt die Berechtigung: %s.',
'err_cannot_stream_output_started' => 'Die Datei kann nicht heruntergeladen werden. Es wurde bereits anderer Inhalt vom Webserver gesendet.',
'err_unknown_file' => 'Datei nicht gefunden.',
'err_no_permission' => 'Dafür fehlt Ihnen die Berechtigung.',
'err_url_scheme' => 'Dieses URL-Schema wird nicht unterstützt. Erlaubte Schemas: %s.',
'err_no_image' => 'Diese Datei ist kein Bild.',
'err_is_deleted ' => 'Dieser Eintrag ist bereits gelöscht.',
'err_text_only_numeric' => 'Dieser Text enthält nur Zahlen und wurde deshalb abgelehnt.',
'err_already_approved' => 'Dieser Eintrag wurde schon freigegeben.',
'err_already_deleted' => 'Dieser Eintrag wurde bereits gelöscht.',
'err_level_too_low' => 'Sie benötigen einen Nutzerlevel von %s um dies zu tun. Ihr Nutzerlevel ist %s.',
'err_unknown_gdo_column' => 'Unbekannte GDO Spalte: %s',
'err_langfile_corrupt' => 'Eine Sprachdatei ist beschädigt: %s',
'err_unknown_config' => 'Unbekannte Konfiguration in Modul %s: %s',
'err_set_cookie' => 'Ihr Cookie konnte nicht ein zweites mal gesetzt werden.',
'err_invalid_gdt_var' => 'Ein GDT Datentyp, %s, hat einen ungültigen Wert: %s.',
'err_unknown_user_setting' => 'Das Modul %s kennt keine Nutzereinstellung namens %s.',
'err_nothing_happened' => 'Nichts hat sich geändert obwohl etwas geschehen sollte.',
'err_local_url_not_allowed' => 'Lokale URL sind nicht erlaubt.',
'err_external_url_not_allowed' => 'Externe URLs sind nicht erlaubt.',
'err_upload_move' => 'Eine Datei konnte nicht von %s nach %s verschoben werden.',
'err_404' => '404 - Seite nicht gefunden',
'err_user_no_permission' => 'Der Benutzer benötigt die %s Berechtigung.',
'err_curl' => 'HTTP Anfrage fehlgeschlagen(%s): %s',
'err_you_no_mail' => 'Sie benötigen eine E-Mail dafür.',
'err_unknown_parameter' => 'Unbekannter Parameter für Methode %s/%s: %s',
'err_select_candidates' => 'Passende Treffer: %s',
    
# File
'is_file' => 'keine Datei',
'is_folder' => 'kein Ordner',
    
# Permissions
'sel_no_permissions' => 'Keine Berechtigung nötig',
'perm_admin' => 'Administrator',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Mitarbeiter',
	
# User types
'enum_bot' => 'Bot',
'enum_ghost' => 'Geist',
'enum_guest' => 'Gast',
'enum_system' => 'System',
'enum_member' => 'Mitglied',

# Checkbox
'enum_undetermined_yes_no' => 'unbekannt',
'enum_unknown' => 'Unbekannt',
	
# Files
'image' => 'Bild',
'icon' => 'Icon',
	
# Gender
'enum_male' => 'männlich',
'enum_female' => 'weiblich',
'enum_no_gender' => 'keine Angabe',

# CRUD
'ft_crud_create' => '%s erstellen',
'ft_crud_update' => '%s bearbeiten',
'msg_crud_created' => '%s wurde erfolgreich erstellt. ID: %s.',
'msg_crud_updated' => '%s wurde erfolgreich bearbeitet.',
'msg_crud_deleted' => '%s wurde erfolgreich gelöscht.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Anwendung',
'sidenav_right_title' => 'Einstellungen',
	
# Config
'ipp' => 'IPP',
'enum_no' => 'Nein',
'enum_yes' => 'Ja',
'cfg_ipp' => 'Einträge pro Seite',
	
# Welcome
'core_welcome_box_info' => 'Willkommen auf %s. Sie sind angemeldet als %s.',
'link_impressum' => 'Impressum',

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

# Method description
'mdescr_core_impressum' => 'Impressum',
'mdescr_core_privacy' => 'Datenschutz',

'li_creation_title' => '%s am %s (%s)',
'edited_info' => 'Zuletzt bearbeitet von %s am %s',
    
# Config
'cfg_system_user' => 'System Benutzer',
'cfg_show_impressum' => 'Impressum im Footer anzeigen?',
'cfg_show_privacy' => 'Datenschutz im Footer anzeigen?',
'cfg_asset_revision' => 'Asset Cache Version',
'link_privacy' => 'Datenschutz',
    
# v6.10
'page_of' => 'Seite %s von %s',
'cfg_spr' => 'Autocomplete Vorschläge pro Anfrage',
'info_page_not_found' => 'Diese Seite existiert nicht. Falls Sie denken, dass es sich um einen Fehler handelt, nehmen Sie bitte Kontakt auf.',

# v6.10
'page_of' => 'Seite %s von %s',
'cfg_spr' => 'Anzahl Vorschläge pro Autocomplete-Requests',
'info_page_not_found' => 'Diese Seite existiert nicht. Wenn Sie denken, dies ist ein Fehler, nehmen Sie bitte Kontakt mit uns auf.',
'cfg_allow_guests' => 'Sind Gästekonten erlaubt?',
'cfg_tt_allow_guests' => 'Aktiviert die Gastfunktionalitäten. Module sollten diese Einstellung prüfen.',
'cfg_siteshort_title_append' => 'Den Seitenkurznamen in Seitentiteln anhängen?',
'cfg_mail_404' => '404 Fehler mails senden?',
'mail_subj_404' => '%s: 404 - Seite nicht gefunden',
'mail_body_404' => '
Hallo %s,<br/>
<br/>
Ein Nutzer hat eine 404 Seite auf %s gefunden.<br/>
<br/>
IP: %s<br/>
Nutzer: %s<br/>
Seite: %s<br/>
Referrer: %s<br/>
<br/>
Viele Grüße,<br/>
%2$s system',

# v6.10.1
'msg_sort_success' => 'Die Einträge wurden neu sortiert.',
'sorting' => 'Sort.',
'mtitle_core_welcome' => 'Willkommen',
'pagemenu_cli' => 'Seite %s / %s',

# v6.10.3 CLI
'cli_methods' => 'Das %s Modul bietet folgende Funktionen: %s.',
'cli_usage' => 'Nutze: %s - %s',
'err_cli' => 'Fehler! %s',
'cli_page' => '%s: %s.',
'msg_new_user_created' => 'Ein neuer Benutzer wurde angelegt: %s',
# v6.10.4 Fixes
'search_term' => 'Suchbegriff',
'cli_pages' => '%s. Seite %s/%s: %s',
'cfg_load_sidebars' => 'Load Sidebar Elements?',
'tt_cfg_load_sidebars' => 'Disable can give a slight performance use, if you use a very custom sidebar / page / theme.',
'thousands_seperator' => '.',
'decimal_point' => ',',
# v6.10.5 Fixes
'min' => 'Min',
'max' => 'Max',
# v6.10.6
'list_core_directoryindex' => '%s Dateien und Ordner',
'date' => 'Datum',
'cfg_footer' => 'Im Footer anzeigen?',
'err_input_not_numeric' => 'Bitte nur Zahlen eingeben.',
'err_missing_template' => 'Eine Template-Datei wird vermisst: %s',

# v6.11.0
'mtitle_core_directoryindex' => 'Verzeichnisinhalt',
	
# v6.11.1
'err_module_disabled' => 'Das Modul %s ist derzeit deaktiviert.',

# v6.11.2
'from' => 'Von',
'to' => 'Bis',
'mail_subj_403' => '%s: 403 Zugang verweigert',
);
