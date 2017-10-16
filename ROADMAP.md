# GDO Roadmap

# TODO

# Nice Module Ideas

- Re-implement Avatar Gallery?
- Convert Module_Usergroup and Module_Guestbook
- Memcached monitor module
- OpenTimes 

# Still todo (issues)

- Change parameter order $code=200, $log=true in GDT_Error and GDT_Success.
- Show query details for perf bar

- Memcached alternative Filecache – on windows just serialize to fs. still some stuff might be worth caching, like LDAP.
- Change cachemiss return type of Memcached to null.

- Modules: 3 iconbuttons: install,configure,adminsection
- Make multiple forms per page really accurate possible by using $form->name[$field->name] in getVar and templates
- GDT_Checkbox filter implementation. Also honor undetermined state 2 
- Pagemenu shows dot too early


# More tuts?

- What is the difference between writable and editable? (writable is still written to db, but cannot be changed from initial – editable accepts user input)

