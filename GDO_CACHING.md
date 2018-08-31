# GDO Cache

## Abstract requirement

In gdo6, my requirement of caching was the following.
Whenever i retrieve an entity of a database, it shall use the same memory adress for the same row.
In all ORM i found, Entity::getById(1) does always return a fresh copy.
GDO does not! It will always return the same object, and even cache it in memcached.
That is, gdoCached() and/or memCached() is true for your GDO.

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


## Implementation Details

There are two caches. "GDO cache" and "memcached cache"(optional).
The GDO cache is empty on process start, like an apache request or restarting the webserver.



- Multiple primary key columns are no problem.
- The memcached key for a GDO entitiy is 
- The 
