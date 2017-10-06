# Caching in gdo6

Caching is a complex topic, and in gdo6, i hopefully got things right.
Actually, the caching in gdo6 is easy, moderately intuitive and actually speed things up :)

On a broad view there are 2 levels of cache in gdo6:

1) GDO process cache
2) memcached global cache
 
Both have their purpose, and there is a reason why i implemented an own caching.


## GDO process cache

The gdo process cache is clean on every request / a new process.
Every instance / row that is returned from the db is checked for an already cached object for this row.
If an instance is in cache, this is returned instead of the fresh results.

This way, we also alter the object that is already in memory, and used in previous or later code and variables.
This really eases the way of coding, mostly in CLI applications, where you might have many references to your entities.

Internally, each GDO / Table is stored into this cache by a tablename_primarykey association.

The same association is used in the memcached global cache. 

## memcached global cache

The GDO\DB\Cache also (optionally) stores entities in memcached to reduce database queries. Remember that on every request the GDO cache would be fresh.

There is also an API to use the memcached directly.
Many modules store various results this way, Often the whole content of a database table, if the expected maximum rowcount is low.
