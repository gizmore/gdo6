<?php
return array(
'sitename' => GWF_SITENAME,

'enum_none' => 'Niente',
'no_selection' => 'Nessuna selezione',

# util/gdo and util/gwf fields
'birthdate' => 'Data di nascita',
'captcha' => 'Capcha',
'color' => 'Colore',
'count' => 'Quantitá',
'country' => 'Natiuone',
'created_at' => 'Creato il',
'created_by' => 'Creato da',
'deleted_at' => 'Cancellato il',
'deleted_by' => 'Cancellato da',
'credits' => 'Credito',
'description' => 'Descrizione',
'email' => 'e-Mail',
'email_fmt' => 'e-Mail Formato',
'enabled' => 'Attivo',
'file' => 'File',
'folder' => 'cartelle di file',
'gender' => 'Cancellato',
'guest' => '~~Ospite~~',
'id' => 'ID',
'info' => 'Info',
'ip' => 'IP',
'language' => 'Lingua',
'level' => 'Livello',
'message' => 'Messaggio',
'name' => 'Nome',
'password' => 'Password',
'path' => 'percorso del file',
'permission' => 'Autorizzazione',
'priority' => 'Prioritá',
'retype' => 'Inserire nuovamente',
'search' => 'Ricerca',
'size' => 'Grandezza',
'sort' => 'Assortire',
'title' => 'Titolo',
'tooltip' => 'Testo di sostegno',
'type' => 'Tipo',
'url' => 'URL',
'user' => 'Utente',
'username' => 'Nome Utente',
'user_real_name' => 'Nome reale',
'version' => 'Versione',	
'not_specified' => 'nessuna indicazione',
'no_match' => 'nessun risultato',
'view' => 'Visualizzare',
'page' => 'Pagina',
'online' => 'Online',
'offline' => 'Offine',
'public' => 'Public',
'private' => 'Private',
'admin' => 'Administration',
	
# Generic Buttons
'btn_back' => 'Indietro',
'btn_set' => 'Posare',
'btn_send' => 'Inviare',
'submit' => 'Inviare',
'btn_save' => 'Memmorizzare',
'btn_upload' => 'Caricare',
'btn_edit' => 'Modificare',
'btn_delete' => 'Cancellare',
'btn_view' => 'Visualizzare',
'btn_preview' => 'Anteprima',
'btn_visible' => 'Imposta visiva',
'btn_invisible' => 'Imposta invisiva ',
'btn_send_mail' => 'Invia e-Mail',
'btn_confirm' => 'Conferma',
'btn_cancel' => 'Abortire',
'link_overview' => 'Sommario',
	
# Generic Messages
'msg_form_saved' => 'I vostri dati sono stati memmorizzati com successo.',
'msg_upgrading' => 'Installare modulo %s versione %s.',
'msg_redirect' => 'in %2$s seconti verrá deviato a <i>%1$s</i>.',

# Generic Confirms
'confirm_delete' => 'Volete cancellare davvero?',
	
# Generic Errors
'err_db' => 'Errore di bancadati(%s): %s<br/>\n%s<br/>\n',
'err_user' => 'Questo utente é sconosciuto.',
'err_exception' => 'Eccezione: %s',
'err_gdo_object_no_table' => '%s é un ogetto GDT senza tabella.',
'err_column' => 'Sconosciuta colonna GDT: %s.',
'err_token' => 'Il suo token non é valido.',
'err_csrf' => 'Il suotoken del formulare non é valido. Evventualmente avete riinviato nuovamente il formulare oppure avete problemi con i cookies. ',
'err_field_invalid' => 'Il vostro valore per %s non é stato accettato.',
'err_blank_response' => 'Il modulo ha dato una risposta vuota. Cosa insolita.',
'err_checkbox_required' => 'Campi da compilare per procedere.',
'err_strlen_between' => 'Questo Testo deve essere lungo fra %s e %s caratteri.',
'err_form_invalid' => 'Il vostro formulare non é completo e contiene errori.',
'err_user_required' => 'Per questa funzione dovete essere registrati. É anche possibile iscriversi <a href="%s">come ospite</a>.',
'err_upload_min_files' => 'Bisogna caricare piú di %s file.',
'err_upload_max_files' => 'Non puó caricare piú di %s file.',
'err_create_dir' => 'Il sommario non ´ha potuto essere compilato: %s ',
'err_permission_required' => 'Occore l`autorizzazione <i>%s</i> per svolgere questa funzione.',
'err_save_unpersisted_entity' => 'É stato provato di salvare una Entity non persistento del tipo <i>%s</i>.',
'err_file_not_found' => 'Non trovato il file: %s',
'err_already_authenticated' => 'É gia registrato.',
'err_gdo_not_found' => '%s non poteva essere trovato con la ID %s.',
'err_string_pattern' => 'Il vostro inserimento non combacia con la maschera di Inserimento.',
'err_url_not_reachable' => 'L´inserimento URL non é stato raggiunto dal Server.',
'err_method_disabled' => 'Questa funzione é disattivata.',
'err_not_null' => 'Questo campo non puó rimanrere vuoto.',
'err_user_type' => 'Il conto non é del tipo %s.',
'err_table_not_sortable' => 'La tabella %s non puó essere ordinata senza un GDT_Sort.',
'err_pass_too_short' => 'Il vostro password deve avere una lungezza di minimo %s caratteri.',
'err_module_method' => 'La funzione non é stata trovata.',
'err_invalid_choice' => 'La vostra selezione è invalida.',
'err_permission_create' => 'Questo ogetto non puó essere creato.',
'err_permission_update' => 'Questo ogetto non puó essere ellaborato.',
'err_permission_delete' => 'Questo ogetto non puó essere Cancellato.',
'err_path_not_exists' => 'Il pfad indicato non é %s: %s',
'err_int_not_between' => 'Il numero deve essere tra %s e %s.',
'err_min_date' => 'Questa data non deve essere %s.',
'err_max_date' => 'Questa data deve essere prima del %s.',
'err_db_connect' => 'Connessione della bancadati fallita.',
'err_db_unique' => 'Questo inserimento é gia esistente.',
'err_image_format_not_supported' => 'Il formato dell´imagine non viene supportato: %s',
'err_members_only' => 'Questa funzione viene messa a disposizione solo ai membri.',
'err_session_required' => 'La vostra sessione è scaduta.',
'err_not_allowed' => 'Le manca l´autorizzazione: %s',
'err_cannot_stream_output_started' => 'Il file non può essere scaricato. Altri contenuti sono già stati inviati dal server web.',
'err_unknown_file' => 'File non trovato.',
	
# Permissions
'sel_no_permissions' => 'Nessuna autorizzazione necessaria.',
'perm_admin' => 'Administratore',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Dippendente',
	
# User types
'enum_ghost' => 'Fantasma',
'enum_guest' => 'Ospite',
'enum_system' => 'Sistema',
'enum_member' => 'Membro',

# Dateformats
'df_day' => '%d.%m.%Y',
'df_short' => '%d.%m.%Y %H:%M',
'tu_s' => 's',
'tu_m' => 'm',
'tu_h' => 'h',
'tu_d' => 'd',
'tu_y' => 'y',

# Checkbox
'enum_undetermined_yes_no' => 'Sconosciuto',
'enum_unknown' => 'Sconosciuto',
	
# Files
'image' => 'Imagine',
'icon' => 'Icona',
	
# Email formats
'enum_html' => 'HTML',
'enum_text' => 'Text',

# Gender
'enum_male' => 'Maschile',
'enum_female' => 'Femminile',
'enum_no_gender' => 'Nessun dato',

# CRUD
'ft_crud_create' => 'Creare %s',
'ft_crud_update' => 'Modifica %s',
'msg_crud_created' => '%s é stato creato con successo.',
'msg_crud_updated' => '%s é stato modificato con successo.',
'msg_crud_deleted' => '%s é stato cancellato con successo.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Applicazione',
'sidenav_right_title' => 'Impostazioni',
	
# Config
'ipp' => 'Iscrizioni per pagina',
'minify_js' => 'Ottimizzare Javascript',
'enum_no' => 'No',
'enum_yes' => 'Si',
'enum_concat' => 'anche riunire',
'cfg_ipp' => 'Voci per pagina',
	
# Welcome
'core_welcome_box_info' => 'Benvenuti su %s. Siete registrati come %s.',
'link_impressum' => 'Area legale',

################################################
'link_node_detect' => 'Cerca programma uglify',
'msg_nodejs_detected' => 'Il programma nodejs è stato trovato: <i>%s</i>',
'msg_annotate_detected' => 'Il programma ng-annotate è stato trovato: <i>%s</i>',
'msg_uglify_detected' => 'Il programma uglify-js è stato trovato: <i>%s</i>',
#
'quote_by' => 'Citato di %s',
'quote_at' => 'il %s',

	
# Filter
'int_filter' => 'Num',
'string_filter' => 'Testo',
'object_filter' => 'Filtro',
'sel_all' => 'Tutti',
'sel_checked' => 'Attivo',
'sel_unchecked' => 'Inattivo',
	
);
