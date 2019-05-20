# GDO Roadmap

Pull requests < gimme your ssh key. :)


# Module Ideas or what is left to convert

- AvatarGallery, Usergroups, Guestbook, Shoutbox
- Memcached monitor module
- OpenTimes
- Updater module using GIT and shell helper
- Shell module/helper for shell commands (see core=>detect node)
- Show query details for perf bar
- Own Memcached alternative: Filecache – on windows just serialize to fs. still some stuff might be worth caching, like LDAP.
- Enhance Installer: Install Single Module, Clear cache
- Ordering is tablesort. Sorting is usersort. Know the difference with auto GDT_Sort field and AjaxDnDSorting?
- Kinda ACL for generic ajax data queries? (6.12)

# Still todo (issues)

- Make module admin a non dependency. (MethodAdmin)
- No output buffering for ultra responsive pages?

- Markup for News,Forum,Links,Downloads is horrible in default design
- Reset ordering by a small button

- Installer: Theme choices are not loading correctly. (module_enabled check?)
- Change cachemiss return type of Memcached to null.
- Change parameter order $code=200, $log=true in GDT_Error and GDT_Success.
- Make multiple forms per page really accurate possible by using $form->name[$field->name] in getVar and templates
- gdo_update.sh can do parallel to speed up
- GDT_Checkbox filter implementation. Also honor undetermined state 2 

- Modules: 3 iconbuttons: install,configure,adminsection

- Pagemenu shows dot too early
- News edit does not show old input

# More tuts?

- What is the difference between writable and editable? (writable is still written to db, but cannot be changed from initial – editable accepts user input)

# Code Ideas
integer sequency enceclopedy modules (OEOIS->sequence('141414')->element(5)->forEach()->etc


