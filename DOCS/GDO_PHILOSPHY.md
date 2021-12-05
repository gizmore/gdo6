# GDO6 Philosophy and code guideline

Noteworthy gdo6 design decisions.

- Everything is a string. You get strings out of the db, out of $_REQUEST vars and memcached... literally everything is a string at the beginning of it's life-cycle. Because of that, gdo6 does not convert data to numbers or other more appropiate classes in the first place. This results in better   performance.

- Any tiny warning or notice can, and, by default,  will result in an application error, log, message,  halt and rollback. Mail with a stacktrace is on it's way.

- Everything uses GDT and GDO. A tiny change in basic GDT like [GDT_String](https://github.com/gizmore/gdo6/blob/master/GDO/DB/GDT_String.php) or [GDT_Int](https://github.com/gizmore/gdo6/blob/master/GDO/DB/GDT_String.php) does affect almost any GDT with var and value handling. Validators are written once and inherited.

- GDT are shared and moved around. This does funny and evil bugs, but it gives a performance boost. For example the GDT_User from a GDO_Comment can be put in a GDT_Form to render a comment form.

