#GDT AND GDO

## GDT
A GDT – or gizmore data type – is something like a component or field, maybe both.
Imagine a world where you can plug the same datatypes into html tables, database entities and web forms.
A GDT always knows how to behave!

A GDT knows about rendering in different contexts, how to create db table code, and how to convert between database and application values and how to validate.

The most basic GDT are GDT_Int, GDT_String, GDT_Enum and GDT_Decimal.
In GDO6; GDT classes are prefixed with GDT_

## GDO

A GDO – or gizmore data object – is a DB-Entity and Table at the same time – although you might not need a database to work with GDOs.
You can think of it like a special, often used GDT, although currently it does not inherit from GDT.
In GDO6; GDO classes are prefixed with GDO_

To create a DB table, simply create a class that inherits from GDO and override the gdoColumns() function.
You simply return an array of GDT, and that's mostly it to work with a DB table and entities.

## Traits

There are many trait helpers to add functionality to your GDT or other classes.
Traits in GDO6 are prefixed with "With...". E.g. WithFields, WithIcon, WithLabel, WithSingleton.
Also some module add new traits, like the voting module adds a WithVotes trait to the GDO eco system.

