# GDO6 Session handling

You have to pick a session handler module in gdo6.
gdo6-session-db and gdo6-session-cookie are available.
You clone one of these modules under the folder name GDO/Session/

## gdo6-session-db

This module variant stores user sessions in the database.

    cd gdo6/GDO
    git clone --recursive https://github.com/gizmore/gdo6-session-db Session
    cd gdo6
    php gdoadm.php install Session

## gdo6-session-cookie

This module (ab)uses cookies to store a users session.
This has the advantage of less database requests, writes in particular.
The bad side is that cookies can be manipulated.
AES encryption is used to prevent that.
Another downside is that large session data volume might slow down requests a bit.

    cd gdo6/GDO
    git clone --recursive https://github.com/gizmore/gdo6-session-cookie Session
    cd gdo6
    php gdoadm.php install Session
