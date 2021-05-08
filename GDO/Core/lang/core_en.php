<?php
return array(
'sitename' => GDO_SITENAME,

'enum_none' => 'None',
'enum_all' => 'All',
'enum_staff' => 'Staff',
'no_selection' => 'No selection',

# util/gdo and util/gwf fields
'all' => 'All',
'captcha' => 'Captcha',
'color' => 'Color',
'count' => 'Count',
'country' => 'Country',
'created_at' => 'Created at',
'created_by' => 'Created by',
'edited_at' => 'Edited at',
'edited_by' => 'Êdited by',
'deleted_at' => 'Deleted at',
'deleted_by' => 'Deleted by',
'credits' => 'Credits',
'description' => 'Description',
'email' => 'Email',
'email_fmt' => 'Email Format',
'enabled' => 'Enabled',
'file' => 'File',
'folder' => 'Folder',
'gender' => 'Gender',
'guest' => '~~Guest~~',
'id' => 'ID',
'info' => 'Info',
'ip' => 'IP',
'language' => 'Language',
'level' => 'Level',
'message' => 'Message',
'name' => 'Name',
'password' => 'Password',
'path' => 'Path',
'permission' => 'Permission',
'priority' => 'Priority',
'retype' => 'Retype',
'search' => 'Search',
'size' => 'Size',
'sort' => 'Sort',
'title' => 'Title',
'tooltip' => 'Tooltip',
'type' => 'Type',
'url' => 'URL',
'user' => 'User',
'username' => 'Username',
'guestname' => 'Guestname',
'user_real_name' => 'Real Name',
'version' => 'Version',
'not_specified' => 'not specified',
'no_match' => 'No match',
'view' => 'View',
'page' => 'Page',
'online' => 'Online',
'offline' => 'Offine',
'public' => 'Public',
'private' => 'Private',
'admin' => 'Administration',
'timezone' => 'Timezone',
'filesize' => 'Filesize',
'font_weight' => 'Font weight',
'perm_level' => 'Permission level',
'menu' => 'Menu',
'download' => 'Download',
'order' => 'Order',
'text' => 'Text',
'num' => 'Num',
'filter' => 'Filter',
'actions' => 'Actions',
    
# Core GDO tables
'gdo_permission' => 'Permission',
    
# Generic Buttons
'btn_back' => 'Back',
'btn_set' => 'Set',
'btn_send' => 'Send',
'submit' => 'Send',
'btn_save' => 'Save',
'btn_upload' => 'Upload',
'btn_edit' => 'Edit',
'btn_create' => 'Create',
'btn_approve' => 'Approve',
'btn_delete' => 'Delete',
'btn_view' => 'View',
'btn_preview' => 'Preview',
'btn_visible' => 'Make visible',
'btn_invisible' => 'Make invisible',
'btn_send_mail' => 'Send Email',
'btn_confirm' => 'Confirm',
'btn_cancel' => 'Cancel',
'btn_overview' => 'Overview',
'btn_search' => 'Search',
'btn_sort' => 'Sort',
    
# Sorting
'order_by' => 'Order by',
'order_dir' => 'Direction',
'asc' => 'Ascending',
'desc' => 'Descending',
'lbl_search_criteria' => 'Criteria: %s',
    
# Generic Messages
'msg_form_saved' => 'Your data has been safed successfully.',
'msg_upgrading' => 'Upgrading Module %s to version %s.',
'msg_redirect' => 'You will be redirected to <i>%s</i> in %s seconds.',

# Generic Confirms	
'confirm_delete' => 'Do you really want to delete this?',
'iconfirm' => 'I am sure',
	
# Generic Errors
'err_db' => "Database Error(%s): %s<br/>\n%s<br/>\n",
'err_user' => 'This user is unknown.',
'err_exception' => 'Exception: %s',
'err_gdo_object_no_table' => '%s is a GDT_Object without a table.',
'err_column' => 'Unknown GDT column: %s.',
'err_token' => 'Your token is invalid.',
'err_csrf' => 'Your form token is invalid. Maybe you have tried to refresh a form submission or have cookie problems.',
'err_field_invalid' => 'Your value for %s is not accepted.',
'err_blank_response' => 'The module gave a blank response, which is unusual.',
'err_checkbox_required' => 'You have to checkmark this field in order to proceed.',
'err_strlen_between' => 'This string has to be between %s and %s characters in length.',
'err_form_invalid' => 'Your sent form is incomplete as it contains errors.',
'err_user_required' => 'You need to be authenticated before using this function. It is also possible to <a href="%s" title="Choose nickname">continue as guest</a>.',
'err_upload_min_files' => 'You have to upload at least %s file(s).',
'err_upload_max_files' => 'You may not upload more than %s files(s).',
'err_create_dir' => 'Could not create directory \'%s\'.',
'err_permission_required' => 'You need <i>%s</i> permissions to access this function.',
'err_save_unpersisted_entity' => 'Tried to save an unpersisted entity of type <i>%s</i>.',
'err_file_not_found' => 'File not found: %s',
'err_already_authenticated' => 'You are already authenticated.',
'err_gdo_not_found' => 'Could not find %s with ID %s.',
'err_string_pattern' => 'Your input did not pass the pattern validation test for this field.',
'err_url_not_reachable' => 'Your entered URL is not reachable by this server.',
'err_method_disabled' => 'This function is currently disabled.',
'err_not_null' => 'This field may not be empty.',
'err_user_type' => 'This account is not of membership type %s.',
'err_table_not_sortable' => 'The %s table cannot be sorted without a GDT_Sort field.',
'err_pass_too_short' => 'Your password has to be at least %s characters long.',
'err_module_method' => 'The module and method could not been not found.',
'err_invalid_choice' => 'Your selection is not applicable.',
'err_permission_create_level' => 'You need a userlevel of %s to create this.',
'err_permission_create' => 'You do not have permissions to create data of this type.',
'err_permission_read' => 'You are not allowed to see this entry.',
'err_permission_update' => 'You do not have permissions to edit this object.',
'err_permission_delete' => 'You do not have permissions to delete this object.',
'err_path_not_exists' => 'The specified path does not qualify as %2$s: %1$s',
'err_int_not_between' => 'This number has to be between %s and %s.',
'err_db_connect' => 'Cannot connect to the database.',
'err_db_unique' => 'This entry already exists.',
'err_image_format_not_supported' => 'The image format is not supported: %s',
'err_members_only' => 'This functionality is for registered members only.',
'err_session_required' => 'Your session has expired.',
'err_not_allowed' => 'You do not have the required permissions: %s.',
'err_cannot_stream_output_started' => 'The file cannot be downloaded. Other content has already been sent from the web server.',
'err_unknown_file' => 'File not found.',
'err_no_permission' => 'You do not have the required permission.',
'err_url_scheme' => 'This URL scheme is not supported. Supported are %s.',
'err_no_image' => 'This file is not an image.',
'err_is_deleted ' => 'This item is already deleted.',
'err_text_only_numeric' => 'This text has only numbers in it and got rejected.',
'err_already_approved' => 'This has been approved already.',
'err_already_deleted' => 'This item has been already deleted.',
'err_level_too_low' => 'You need a userlevel of %s to do that. Your level is %s.',
'err_unknown_gdo_column' => 'Unknown GDO column: %s',
'err_langfile_corrupt' => 'A languagefile is corrupt: %s',
'err_unknown_config' => 'Unknown config field in module %s: %s',
'err_set_cookie' => 'Your cookie could not been set a second time.',
'err_invalid_gdt_var' => 'A GDT, %s, has an invalid var: %s.',
'err_unknown_user_setting' => 'The module %s does not know the user setting %s.',
'err_nothing_happened' => 'Nothing has changed despite your action.',
'err_external_url_not_allowed' => 'External URL are not allowed.',
'err_upload_move' => 'File could not be moved from %s to %s.',
'err_404' => '404 - File not found',
'err_user_no_permission' => 'The user needs the %s permission.',
    
# File
'is_file' => 'a File',
'is_folder' => 'a Folder',
    
# Permissions
'sel_no_permissions' => 'No permission required',
'perm_admin' => 'Administrator',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Staff',

# User types
'enum_bot' => 'Bot',
'enum_ghost' => 'Ghost',
'enum_guest' => 'Guest',
'enum_system' => 'System',
'enum_member' => 'Member',
	
# Checkbox
'enum_undetermined_yes_no' => 'unknown',
'enum_unknown' => 'unknown',

# Files
'image' => 'Image',
'icon' => 'Icon',
	
# Gender
'enum_male' => 'male',
'enum_female' => 'female',
'enum_no_gender' => 'not specified',

# CRUD
'ft_crud_create' => 'Create %s',
'ft_crud_update' => 'Edit %s',
'msg_crud_created' => 'Your %s has been created.',
'msg_crud_updated' => 'Your %s has been updated.',
'msg_crud_deleted' => 'Your %s has been deleted.',

# Sidebar
'gdo_sidebar_version' => 'gdo %s',
'sidenav_left_title' => 'Application',
'sidenav_right_title' => 'Settings',
	
# Config
'ipp' => 'Items per page (ipp)',
'enum_no' => 'No',
'enum_yes' => 'Yes',
'cfg_ipp' => 'Items per page',
	
# Welcome
'core_welcome_box_info' => 'Welcome to %s. You are authenticated as %s.',
'link_impressum' => 'Imprint',

#
'quote_by' => 'Quote by %s',
'quote_at' => 'at %s',
	
# Filter
'int_filter' => 'Num',
'string_filter' => 'Text',
'object_filter' => 'Filter',
'sel_all' => 'All',
'sel_checked' => 'Active',
'sel_unchecked' => 'Inactive',

# Method description
'mdescr_core_impressum' => 'Site impressum',
'mdescr_core_privacy' => 'Privacy information',
    
'li_creation_title' => '%s at %s (%s)',
'edited_info' => 'Last edited by %s at %s',

# Config
'cfg_system_user' => 'System user',
'cfg_show_impressum' => 'Show impressum in footer?',
'cfg_show_privacy' => 'Show privacy in footer?',
'cfg_asset_revision' => 'Asset revision cache poisoner',
'link_privacy' => 'Privacy',

# v6.10
'page_of' => 'Page %s of %s',
'cfg_spr' => 'Autocomplete suggestions per request',
'info_page_not_found' => 'The page you are looking for does not exist. If you think this is an error, please contact us.',
'cfg_allow_guests' => 'Are guest accounts allowed?',
'cfg_tt_allow_guests' => 'Enables guest functionality, globally.',
'cfg_siteshort_title_append' => 'Append sitename to page titles?',
'cfg_mail_404' => 'Send 404 Error Mails?',
'mail_subj_404' => '%s: 404 File not found',
'mail_body_404' => '
Hello %s,<br/>
<br/>
A client has found a 404 page on %s.<br/>
<br/>
IP: %s<br/>
User: %s<br/>
Page: %s<br/>
Referrer: %s<br/>
<br/>
Kind Regards,<br/>
%2$s system',
    
# v6.10.1
'msg_sort_success' => 'The items have been re-arranged.',
'sorting' => 'Sorting',
'mtitle_core_welcome' => 'Welcome',
'pagemenu_cli' => 'Page %s of %s',
);
