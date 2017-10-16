# GDO Roadmap

- 

# TODO

- Change parameter order $code=200, $log=true in GDT_Error and GDT_Success.
- queries in perf bar

# Nice Module Ideas

- Re-implement Avatar Gallery?
- Convert Module_Usergroup and Module_Guestbook
- Memcached monitor module

# Still todo (issues)

- OpenTimes
- Memcached alternative Filecache – on windows just serialize to fs. still some stuff might be worth caching, like LDAP.
- Change cachemiss return type of Memcached to null.

- Modules: 3 iconbuttons: install,configure,adminsection
- Make multiple forms per page really accurate possible by using $form->name[$field->name] in getVar and templates
- GDT_Checkbox filter implementation. Also honor undetermined state 2 

# More tuts?

- What is the difference between writable and editable? (writable is still written to db, but cannot be changed from initial – editable accepts user input)
