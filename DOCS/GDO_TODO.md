# TODO

This is a todo list that did not make it into a code annotation yet, or are just ideas.
For a more complete todo, please see GDO_TODO_AUTO_GENERATED.md


- @TODO: Make use of user_type bot. Write a module that identifes bots by their user agent. What else?

- @TODO: Create a method Register::elevateGuest() to turn guests into members. But first think of a concept for that. Concept: Just have a checkbox "make_this_guest_a_member" if user->hasGuestName() && $user->isGuest()

- @TODO: Add a table GDO_I18n which holds additional translation data editable via website. (see module LanguageEditor)

- @TODO: Add postgres. (thx Nekomander) - This can be done by making module DB an own repo. Just provide us with an impl of [Module_DB](https://github.com/gizmore/gdo6/edit/master/GDO/DB/) that uses postgres only. @TODO there are more classes that generate SQL COLUMN code. All these GDT which provide DB code need to be in /DB?

- @TODO: Make gdo6 sqlite compatible. Avoid IFs but pass closures around.

- @TODO: Use a real mailer library. Make two modules. gdo6-mail and gdo6-mail-foomailer. Make Mail a normal module, not a core module.

- @TODO: Write module gdo6-impressum. Remove impressum stuff from core. Allow only an url to be set. default is a page with a translation key used?

- @TODO: Enhance I18n Trans class. t(key, [args]) shall be changed to t(key, ...args)

- @TODO: Move GDT_ACL from gdo6-friends to gdo6 core. A check if module_friends is available can stay in GDT_ACL code.

- @TODO: gdo6-session-db can use precalculated sessids to fix the ugly problem of requiering a 2nd request for a real saved session.

- @TODO: move method ClearCache from Module_Admin to Module_Core.

- @TODO: Add security gpg key for security.txt contact.

- @TODO: Module_Login.force_auth ... if enabled, only login would work unless authenticated. Steal from lup-project.

- @TODO: Allow to create gui apps with QT or GTK+ or something. Just a wrapper for CLI, but similiar to html layout.

- @TODO: On module init, set up the hooks, so not every module has to be tested. Looking forward to another ms saved.

- @TODO: Module_Heartbeat or an implementation in OnlineUsers.

- @TODO: Module_Benchmark to benchmark some stuff. maybe make it rely on Module_CountryCoordinates for bulk and many insert stuff.

- @TODO: create a composer package for the gdo6 core. Further packages can still be maintained with gdoadm.sh

- @TODO: finish the [Helpdesk](https://github.com/gizmore/gdo6-helpdesk) coding tutorial.

