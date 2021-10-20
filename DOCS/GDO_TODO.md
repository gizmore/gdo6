# TODO

This is a todo list that did not make it into a code annotation yet.
For a complete todo, please grep -R --case-i "@TODO" GDO/.


- @TODO: Make Query::order() to use one param "date desc" instead of 2 params "date", false. (DONE, it is not as nice and fast but feels more easy to use->order('uer_level DESC'). Before it was use->order('user_level', false). Maybe the old way with constants is best?: ->order('user_level', Query::DESC) or ->orderDESC('user_level). nah!

- @TODO: Make use of user_type bot. Write a module that identifes bots by their user agent. What else?

- @TODO: Create a method Register::elevateGuest() to turn guests into members. But first think of a concept for that. Concept: Just have a checkbox "make_this_guest_a_member" if user->hasGuestName() && $user->isGuest()

- @TODO: Add a table GDO_I18n which holds additional translation data editable via website. (see module LanguageEditor)

- @TODO: Add postgres. (thx Nekomander) - This can be done by making module DB an own repo. Just provide us with an impl of [Module_DB](https://github.com/gizmore/gdo6/edit/master/GDO/DB/) that uses postgres only. @TODO there are more classes that generate SQL COLUMN code. All these GDT which provide DB code need to be in /DB?

- @TODO: Use a real mailer library. Make two modules. gdo6-mail and gdo6-mail-foomailer. Make Mail a normal module, not a core module.

- @TODO: Write module gdo6-impressum. Remove impressum stuff from core. Allow only an url to be set. default is a page with a translation key used?

- @TODO: Enhance I18n Trans class. t(key, [args]) shall be changed to t(key, ...args)

- @TODO: Move GDT_ACL from gdo6-friends to gdo6 core.

- @TODO: gdo6-session-cookie shall not use fake cookies. It can fix this ugly problem of requiering a 2nd request for a cookie.

- @TODO: gdo6-session-db can use precalculated sessids to fix the ugly problem of requiering a 2nd request for a real saved session.

- @TODO: move method ClearCache from Module_Admin to Module_Core.

- @TODO: Add a (database) lock to the javascript minifier, so only one thread/user is performing it, the others have to wait. Lock timeout maybe 90s . Move Util/JS stuff to the GDO/Javascript module.

- @TODO: Add security gpg key for security.txt contact.

- @TODO: Implement auto migrations via: dump->wipe->install->reimport. Trigger this via a list of revisions that require it. Measure revision before and after update. If revision is between this, trigger a wipe-reinstall.

- @TODO: Module_Login.force_auth ... if enabled, only login would work unless authenticated.

- @TODO: Make a table for last cronjob execution. Create MethodCronjob function every($duration) and compare with last execution to make creating periodic cronjobs way easier. Some cronjobs already handle periodicy theyself. Make them use the new system.

- @TODO: Create a command to list all gdo6 related todos from all modules.



