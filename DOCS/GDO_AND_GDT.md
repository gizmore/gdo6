# GDT AND GDO

## GDT
A GDT – or gizmore data type – is something like a component or field, maybe both.
Imagine a world where you can plug the same datatypes into html tables, database entities and web forms.
A GDT always knows how to behave!

A GDT knows about rendering in different contexts, how to create db table code, how to filter queries and sort GDOs, how to validate and how to convert to and from database (vars) and application objects (values).

The most basic GDT are GDT_Int, GDT_String, GDT_Enum and GDT_Decimal.
But there are dozens of GDT available like GDT_Object, GDT_Email, GDT_File, GDT_JSON, GDT_Checkbox, GDT_AutoInc, GDT_JOIN, etc... And you don't need to write a single validator ever! (Maybe with the exception of custom GDT_Validator fields.
GDT_Object, which inherits from GDT_UInt, creates a nice foreign key constraint which you can control with the ->cascade*() methods; cascade(), cascadeNull() and cascadeRestrict(), but 
the defaults are very likely to be perfect.
In official GDO6 modules GDT classes are prefixed with "GDT_".

## GDO

A GDO – or gizmore data object – is a DB entity and table at the same time – although you might not need a database to work with GDOs.
You can think of it like a special, often used GDT, although currently it does not inherit from GDT.
In official GDO6 modules GDO classes are prefixed with "GDO_".

To create a DB table, simply create a class that inherits from GDO and override the gdoColumns() function.
You simply return an array of GDT, and that's mostly it to work with a DB table and entities.
Make sure you announce the GDO in your module->getClasses() to get it installed easily.

GDO's support combined primary keys and offer two levels of caching, which is default enabled for all GDO.
You can disable the caches by overriding the gdoCached() and gdoMemcached() methods.
Alternatively you can use ->uncached() on a Query to disable the cache for a single query.
You can read more about GDO caching in the caching documentation-

## Traits

There are many trait helpers to add functionality to your GDT.
Traits in GDO6 are prefixed with "With*". E.g. "WithFields", "WithIcon", "WithLabel", "WithSingleton".
Also some modules add new traits, like the voting module adds a "WithVotes" trait to the GDO eco system.
