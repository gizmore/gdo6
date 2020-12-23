# GDO

The DataBase Abstraction layer.
Here are some tutorials to get you started with GDO.


## Basics

The base class GDO is both, entity and table.
Every class has one object stored in the Database to describe all entities.
Columns are defined in the gdoColumns() method, returning an array of GDT.
Please note that GDO is not SQL injection safe, but this reduces code complexity a lot.


## Classes

The following classes make up the GDO DBA.

- https://github.com/gizmore/gdo6/blob/master/GDO/DB/Database.php
- https://github.com/gizmore/gdo6/blob/master/GDO/DB/Query.php
- https://github.com/gizmore/gdo6/blob/master/GDO/DB/Result.php
- https://github.com/gizmore/gdo6/blob/master/GDO/DB/ArrayResult.php (for non db stuff)
- https://github.com/gizmore/gdo6/blob/master/GDO/DB/Cache.php


## Caching

See https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_CACHING.md

## Column types

Every GDT can be used as a column, but the default GDT does not feature this.
There is a trait WithDatabase that should be added if a GDT supports it.
A GDT that supports this has to override gdoColumnDefine() for database column creation code.
Some GDT even create multiple columns, like GDT_Message stores a fulltext field or GDT_Position has lat and long.


## Basic examples

find the system user via a user by id:

    GDO_User::find(1);
    
find the system user via user_type:

    GDO_User::findBy('user_type', 'system);
    
create a new user:

    GDO_User::blank(['user_name' => 'TestUser', 'user_type' => 'member'])->insert();
    
find all members

    GDO_User::table()->select()->where("user_type='member'")->exec()->fetchAllObjects()
    
find all admins:

    GDO_UserPermission:table()->select('gdo_user.*')->joinObject('perm_user')->fetchAllObjectsAs(GDO_User::table());
    
delete all guests and trigger all delete hooks on those:

    GDO_User::table()->deleteWhere("user_type='guest'");
    
delete the user Peter:

    GDO_User::table()->delete()->where("user_name='Peter'")->exec()
    
delete the user Peter and trigger deletion hook:

    GDO_User::table()->select()->where("user_name='Peter'")->exec()->fetchObject()->delete()
    
    
 
    
