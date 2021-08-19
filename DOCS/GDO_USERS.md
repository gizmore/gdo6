# The user system in GDO6

GDO6 loves guests. In fact, everyone is a guest sometimes, and sometimes you also serve guests to make friends.
Because of that, guest is a special user type you can achieve if you register as a guest with a nickname.
Many modules allow guests to participate in creating the web if they only have a cookie and a nickname.
Else, the user system in GDO6 is quite hierarchic and we also have auth providers via twitter and facebook.
Methods can restrict access by user_type, permission, user_level and of course arbritary functions.

## Modules that add sign up methods

https://github.com/gizmore/gdo6-facebook
https://github.com/gizmore/gdo6-instagram
https://github.com/gizmore/gdo6-register (also features guests)


## User types

There are the following 5 user types:

- ghosts (probably a spider or just a fresh connection)
- guests (have chosen a guest name via Register::Guest, working cookie)
- members (have registered as a member (with email, if configured)
- system (user_id:1, used when no user is available)
- bot (identified bot, currently unused)

Guests and Members qualify as authenticated if logged in / authenticated.
Remember, a guest has chosen a guestname by Register::Guest


## Permissions

Additionally there is a permission system.
Currently there are 3 permissions used by the gdo6 eco-system.
Permissions can be added freely.

- admin
- staff
- cronjob

## Level

Finally, there is the user_level property which can also be used to restrict methods to a user.
Permissions also have a level defined, which highest is your minlevel as user_level property.



