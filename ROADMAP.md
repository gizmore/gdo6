# GDO Roadmap

Pull requests < gimme your ssh key. :)


# Module Ideas or what is left to convert

- AvatarGallery, Usergroups, Guestbook, Shoutbox
- Memcached monitor module
- OpenTimes
- Updater module using GIT
- Shell module for shell interaction (see core=>detect node)
- Show query details for perf bar
- Own Memcached alternative: Filecache – on windows just serialize to fs. still some stuff might be worth caching, like LDAP.


# Still todo (issues)

- Markup for News,Forum,Links,Downloads is horrible in default design

- Change cachemiss return type of Memcached to null.
- Change parameter order $code=200, $log=true in GDT_Error and GDT_Success.
- Make multiple forms per page really accurate possible by using $form->name[$field->name] in getVar and templates

- GDT_Checkbox filter implementation. Also honor undetermined state 2 

- Modules: 3 iconbuttons: install,configure,adminsection

- Pagemenu shows dot too early


# More tuts?

- What is the difference between writable and editable? (writable is still written to db, but cannot be changed from initial – editable accepts user input)

