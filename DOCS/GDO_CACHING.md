# GDO Cache

## Abstract requirement

In gdo6, my requirement of caching was the following.

Whenever I retrieve an entity of a database, it shall use the same memory address for the same row.

In all ORM I found, Entity::getById(1) does always return a fresh copy.

GDO does not! It will always return the same object, and can even cache it in memcached.

That is, gdoCached() and/or memCached() is true for your GDO.

I got told the idea is not new. For example PONY ORM also should have a single identity cache, but i could not confirm this yet.

Example:

    GDO_User::findById(1) === GDO_User::findById(1) # => true
    GDO_User::table()->select()->where('user_id=1')->exec()->fetchObject() === GDO_User::findById(1) # => true

## API

When you create a GDO class, simply override the two caching definition methods.
In this example we turn of caching. maybe because its a relation table or the data expected is huge and not worth caching.

    class MyEntitiy extends GDO
    {
    	public function gdoCached() { return false; } # default to true
    	public function memCached() { return false; } # default to true
    }


# Implementation Details

There are 3 types of cache in gdo.

 - Process cache
 - Memcached
 - Filecache


## GDO process cache

The gdo process cache is clean on every request / a new process.

Every instance / row that is returned from the db is checked for an already cached object for this row.

If an instance is in cache, this is returned instead of the fresh results.

This way, we also alter the object that is already in memory, and used in previous or later code and variables.

This really eases the way of coding, mostly in CLI applications, where you might have many references to your entities.

Internally, each GDO / Table is stored into this cache by a tablename_primarykey association.

The same association is used in the memcached global cache.


## Memcached global cache

The GDO\DB\Cache also (optionally) stores entities in memcached to reduce database queries. Remember that on every request the GDO cache would be fresh.

There is also an API to use the memcached directly.

Many modules store various results this way, Often the whole content of a database table, if the expected maximum rowcount is low. An example would be the GDO_Country table.


## Filecache

New since 6.0.3 is the file cache.
It uses the file system to cache the output of executing a method.
Currently the filename consists of the method name and the gdoParamteres used to invoke it. Additionally the language code is added to it.

