<?php
return array(
'sitename' => GWF_SITENAME,

'no_selection' => 'No selection',

# util/gdo and util/gwf fields
'id' => 'ID',
'file' => 'File',
'folder' => 'Folder',
'title' => 'Title',
'description' => 'Description',
'info' => 'Info',
'message' => 'Message',
'captcha' => 'Captcha',
'user' => 'User',
'ip' => 'IP',
'username' => 'Username',
'gender' => 'Gender',
'email' => 'Email',
'email_fmt' => 'Email Format',
'language' => 'Language',
'country' => 'Country',
'password' => 'Password',
'user_real_name' => 'Real Name',
'name' => 'Name',
'enabled' => 'Enabled',
'sort' => 'Sort',
'version' => 'Version',
'path' => 'Path',
'retype' => 'Retype',
'user_allow_email' => 'Allow people to E-Mail me',
'birthdate' => 'Birthdate',
'url' => 'URL',
'guest' => '~~Guest~~',
'size' => 'Size',
'type' => 'Type',
'level' => 'Level',
'count' => 'Count',
'credits' => 'Credits',
'permission' => 'Permission',
'created_at' => 'Created at',
'created_by' => 'Created by',

# Generic Buttons
'btn_send' => 'Send',
'btn_save' => 'Save',
'btn_upload' => 'Upload',

# Generic Messages
'msg_form_saved' => 'Your data has been safed successfully.',
'msg_upgrading' => 'Upgrading Module %s to version %s.',
'msg_redirect' => 'You will be redirected to <i>%s</i> in %s seconds.',

# Generic Errors
'err_db' => "Database Error: %s<br/>\n%s<br/>\n",
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
'err_permission_required' => 'You need <i>%s</i> permissions to access this function.',
'err_save_unpersisted_entity' => 'Tried to save an unpersisted entity of type <i>%s</i>.',
'err_file' => 'File not found: %s',
'err_already_authenticated' => 'You are already authenticated.',
'err_gdo_not_found' => 'Could not find %s with ID %s.',
'err_string_pattern' => 'Your input did not pass the pattern validation test for this field.',
'err_url_not_reachable' => 'Your entered URL is not reachable by this server.',
'err_method_disabled' => 'This function is currently disabled.',
'err_not_null' => 'This field may not be empty.',
'err_user_type' => 'Your account is not of membership type %s.',
'err_table_not_sortable' => 'The %s table cannot be sorted without a GDT_Sort field.',
'err_pass_too_short' => 'Your password has to be at least %s characters long.',
'err_module_method' => 'The module and method could not been not found.',
'err_invalid_choice' => 'Your selection is not applicable.',
'err_permission_create' => 'You do not have permissions to create data of this type.',
'err_permission_update' => 'You do not have permissions to edit this object.',
'err_path_not_exists' => 'The specified path does not qualify as %s: %s',
'err_int_not_between' => 'This number has to be between %s and %s.',
'err_min_date' => 'This date has to be after %s.',
'err_max_date' => 'This date has to be before %s.',
'err_db_unique' => 'This entry already exists.',
# Permissions
'perm_admin' => 'Administrator',
'perm_cronjob' => 'Cronjob',
'perm_staff' => 'Staff',

# Dateformats
'df_day' => 'm/d/Y',
'df_short' => 'm/d/Y H:i',
'tu_s' => 's',
'tu_m' => 'm',
'tu_h' => 'h',
'tu_d' => 'd',
'tu_y' => 'y',

# Files
'fsiso' => array('B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'YB', 'ZB'),

# Email formats
'enum_html' => 'HTML',
'enum_text' => 'Text',

# Gender
'enum_male' => 'male',
'enum_female' => 'female',
'enum_no_gender' => 'not specified',

# CRUD
'ft_crud_create' => '[%s] Create %s',
'ft_crud_update' => '[%s] Edit %s',
'msg_crud_created' => 'Your %s has been created.',
'msg_crud_updated' => 'Your %s has been updated.',
'msg_crud_deleted' => 'Your %s has been deleted.',

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
