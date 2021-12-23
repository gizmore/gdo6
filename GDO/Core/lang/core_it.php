<?php
namespace GDO\Core\lang;
return array(
'sitename' => GDO_SITENAME,

'enum_none' => 'Niente',
'enum_all' => 'Tutti',
'enum_staff' => 'Dipendente',
'no_selection' => 'Nessuna selezione',

# util/gdo and util/gwf fields
'all' => 'Tutti',
'captcha' => 'Capcha',
'color' => 'Colore',
'count' => 'Quantitá',
'country' => 'Natiuone',
'created_at' => 'Creato il',
'created_by' => 'Creato da',
'edited_at' => 'Modificato il',
'edited_by' => 'Modificato da',
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
'guestname' => 'Nome dell\'ospite',
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
'timezone' => 'Timezone',
'filesize' => 'Dimensione del file',
'font_weight' => 'Forza dei caratteri',
'perm_level' => 'Livello di autorizzazione',
'menu' => 'Menù',
'download' => 'Scarica',
'order' => 'Sequenza',
'text' => 'Testo',
'num' => 'Numero',
'filter' => 'Filtro',
'actions' => 'Azioni',
'deleted' => 'Cancellato',
'completed' => 'Fatto',
'timezone' => 'Fuso orario',
'format' => 'Formato',
    
# Core GDO tables
'gdo_permission' => 'Autorizzazione',
    
# Generic Buttons
'btn_ok' => 'OK',
'btn_back' => 'Indietro',
'btn_set' => 'Posare',
'btn_send' => 'Inviare',
'submit' => 'Inviare',
'btn_save' => 'Memmorizzare',
'btn_upload' => 'Caricare',
'btn_edit' => 'Modificare',
'btn_create' => 'Creare',
'btn_approve' => 'Sbloccare',
'btn_delete' => 'Cancellare',
'btn_view' => 'Visualizzare',
'btn_preview' => 'Anteprima',
'btn_visible' => 'Imposta visiva',
'btn_invisible' => 'Imposta invisiva ',
'btn_send_mail' => 'Invia e-Mail',
'btn_confirm' => 'Conferma',
'btn_cancel' => 'Abortire',
'btn_overview' => 'Sommario',
'btn_search' => 'Ricerca',
'btn_sort' => 'Ordina',
    
# Sorting
'order_by' => 'Ordina per',
'order_dir' => 'Direzione',
'list_order' => 'Ordina per: %s, %s',
'asc' => 'Ascendente',
'desc' => 'Discendente',
'lbl_search_criteria' => 'Ricerca: %s',
    
# Generic Messages
'msg_form_saved' => 'I vostri dati sono stati memmorizzati com successo.',
'msg_upgrading' => 'Installare modulo %s versione %s.',
'msg_redirect' => 'in %2$s seconti verrá deviato a <i>%1$s</i>.',

# Generic Confirms
'confirm_delete' => 'Volete cancellare davvero?',
'iconfirm' => 'sono sicuro',
    
# Generic Errors
'err_db' => 'Errore di bancadati(%s): %s<br/>\n%s<br/>\n',
'err_user' => 'Questo utente é sconosciuto.',
'err_exception' => 'Eccezione: %s',
'err_parameter_exception' => '%s: %s',
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
'err_no_data_yet' => 'Non ci sono ancora dati disponibili.',
'err_string_pattern' => 'Il vostro inserimento non combacia con la maschera di Inserimento.',
'err_url_not_reachable' => 'L´inserimento URL non é stato raggiunto dal Server.',
'err_method_disabled' => 'Questa funzione é disattivata.',
'err_not_null' => 'Questo campo non puó rimanrere vuoto.',
'err_user_type' => 'L\'account non è un tipo di %s.',
'err_table_not_sortable' => 'La tabella %s non puó essere ordinata senza un GDT_Sort.',
'err_pass_too_short' => 'Il vostro password deve avere una lungezza di minimo %s caratteri.',
'err_module' => 'Impossibile trovare il modulo: %s.',
'err_module_method' => 'La funzione non é stata trovata.',
'err_invalid_choice' => 'La vostra selezione è invalida.',
'err_permission_create_level' => 'È necessario un livello utente di %s per creare questo.',
'err_permission_create' => 'Questo ogetto non puó essere creato.',
'err_permission_read' => 'Non sei autorizzato a vedere questa voce.',
'err_permission_update' => 'Questo ogetto non puó essere ellaborato.',
'err_permission_delete' => 'Questo ogetto non puó essere Cancellato.',
'err_path_not_exists' => 'Il Posizione indicato non %2$s: %1$s',
'err_int_not_between' => 'Il numero deve essere tra %s e %s.',
'err_db_connect' => 'Connessione della bancadati fallita.',
'err_db_unique' => 'Questo inserimento é gia esistente.',
'err_image_format_not_supported' => 'Il formato dell´imagine non viene supportato: %s',
'err_members_only' => 'Questa funzione viene messa a disposizione solo ai membri.',
'err_session_required' => 'La vostra sessione è scaduta.',
'err_not_allowed' => 'Le manca l´autorizzazione: %s',
'err_cannot_stream_output_started' => 'Il file non può essere scaricato. Altri contenuti sono già stati inviati dal server web.',
'err_unknown_file' => 'File non trovato.',
'err_no_permission' => 'Non sei autorizzato a farlo.',
'err_url_scheme' => 'Questo schema URL non è supportato. Sono supportati %s.',
'err_no_image' => 'Questo file non è un \'immagine.',
'err_is_deleted ' => 'Questa voce è già stata cancellata.',
'err_text_only_numeric' => 'Questo testo contiene solo numeri ed è stato quindi respinto.',
'err_already_approved' => 'Questa voce è già stata rilasciata.',
'err_already_deleted' => 'Questa voce è già stata eliminata.',
'err_level_too_low' => 'È necessario un livello utente di %s per farlo. Il tuo livello utente è %s.',
'err_unknown_gdo_column' => 'Colonna GDO sconosciuta: %s',
'err_langfile_corrupt' => 'Un file di lingua è danneggiato: %s',
'err_unknown_config' => 'Configurazione sconosciuta nel modulo %s: %s',
'err_set_cookie' => 'Il tuo cookie non può essere impostato una seconda volta.',
'err_invalid_gdt_var' => 'Un tipo di dati GDT, %s, ha un valore non valido: %s.',
'err_unknown_user_setting' => 'Il modulo %s non ha alcuna impostazione utente chiamata %s.',
'err_nothing_happened' => 'Niente è cambiato anche se qualcosa sarebbe dovuto accadere.',
'err_local_url_not_allowed' => 'Gli URL locali non sono consentiti.',
'err_external_url_not_allowed' => 'Gli URL esterni non sono consentiti.',
'err_upload_move' => 'Non è stato possibile spostare un file da %s a %s.',
'err_404' => '404 - Pagina non trovata',
'err_user_no_permission' => 'Nessuna autorizzazione: %s.',
'err_curl' => 'Richiesta HTTP non riuscita(%s): %s',
'err_you_no_mail' => 'Hai bisogno di un\'e-mail per questo.',
'err_unknown_parameter' => 'Parametro sconosciuto per il metodo %s/%s: %s',
'err_select_candidates' => 'Hit corrispondenti: %s',
    
# File
'is_file' => 'un File',
'is_folder' => 'una Cartella',

# Permissions
'sel_no_permissions' => 'Nessuna autorizzazione necessaria.',
'perm_admin' => 'Administratore',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Dippendente',
	
# User types
'enum_bot' => 'Bot',
'enum_ghost' => 'Fantasma',
'enum_guest' => 'Ospite',
'enum_system' => 'Sistema',
'enum_member' => 'Membro',

# Checkbox
'enum_undetermined_yes_no' => 'Sconosciuto',
'enum_unknown' => 'Sconosciuto',
	
# Files
'image' => 'Imagine',
'icon' => 'Icona',
	
# Gender
'enum_male' => 'Maschile',
'enum_female' => 'Femminile',
'enum_no_gender' => 'Nessun dato',

# CRUD
'ft_crud_create' => 'Creare %s',
'ft_crud_update' => 'Modifica %s',
'msg_crud_created' => '%s é stato creato con successo. ID: %s.',
'msg_crud_updated' => '%s é stato modificato con successo.',
'msg_crud_deleted' => '%s é stato cancellato con successo.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Applicazione',
'sidenav_right_title' => 'Impostazioni',
	
# Config
'ipp' => 'IPP',
'enum_no' => 'No',
'enum_yes' => 'Si',
'cfg_ipp' => 'Voci per pagina',
	
# Welcome
'core_welcome_box_info' => 'Benvenuti su %s. Siete registrati come %s.',
'link_impressum' => 'Area legale',

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
	
# Method description
'mdescr_core_impressum' => 'Site impressum',
'mdescr_core_privacy' => 'Privacy information',

'li_creation_title' => '%s il %s (%s)',
'edited_info' => 'Zuletzt bearbeitet von %s am %s',
    
# Config
'cfg_system_user' => 'System Benutzer',
'cfg_show_impressum' => 'Impressum im Footer anzeigen?',
'cfg_show_privacy' => 'Datenschutz im Footer anzeigen?',
'cfg_asset_revision' => 'Asset Cache Version',
'link_privacy' => 'Datenschutz',

# v6.10
'page_of' => 'Pagina %s di %s',
'cfg_spr' => 'Suggerimenti per il completamento automatico per richiesta',
'info_page_not_found' => 'Questa pagina non esiste. Se pensi che ci sia un errore, contattaci.',
'cfg_allow_guests' => 'Gli account ospiti sono consentiti?',
'cfg_tt_allow_guests' => 'Attiva le funzioni guest. I moduli dovrebbero controllare questa impostazione.',
'cfg_siteshort_title_append' => 'Aggiungi il nome breve della pagina nei titoli delle pagine?',
'cfg_mail_404' => 'Inviare messaggi di errore 404?',
'mail_subj_404' => '%s: 404 - Pagina non trovata',
'mail_body_404' => '
Salve %s,<br/>
<br/>
Un utente ha trovato una pagina 404 su %s.<br/>
<br/>
IP: %s<br/>
Utente: %s<br/>
Pagina: %s<br/>
Referente: %s<br/>
<br/>
Cordiali saluti,<br/>
%2$s sistema',

# v6.10.1
'msg_sort_success' => 'Le voci sono state riordinate.',
'sorting' => 'Ordi.',
'mtitle_core_welcome' => 'Benvenuto',
'pagemenu_cli' => 'Pagina %s / %s',

# v6.10.3 CLI
'cli_methods' => 'Il modulo %s offre le seguenti funzioni: %s.',
'cli_usage' => 'Uso: %s - %s',
'err_cli' => 'Errore! %s',
'cli_page' => '%s: %s.',
'msg_new_user_created' => 'È stato creato un nuovo utente: %s',
    
# v6.10.4 Fixes
'search_term' => 'ricerca',
'cli_pages' => '%s. Pagina %s/%s: %s',
'cfg_load_sidebars' => 'Load Sidebar Elements?',
'cfg_tt_load_sidebars' => 'Disable can give a slight performance use, if you use a very custom sidebar / page / theme.',
'thousands_seperator' => '.',
'decimal_point' => ',',
    
# v6.10.5 Fixes
'min' => 'Min',
'max' => 'Max',

# v6.10.6 Fixes
'list_core_directoryindex' => '%s File e cartelle',
'date' => 'Data',
'cfg_footer' => 'Mostra nel piè di pagina?',
'err_input_not_numeric' => 'Si prega di inserire solo numeri.',
'err_missing_template' => 'Manca un file modello: %s',

# v6.11.0
'mtitle_core_directoryindex' => 'Contenuto della directory',

# v6.11.1
'err_module_disabled' => 'Il modulo %s è attualmente disattivato.',
);
