# History

gdo6 Changelog.


## 6.10.5 (15.09.2021)

 - Time based GDT, like Date, DateTime and Timestamp now return a \DateTime object in toValue().

 - Module_Birthday allows to age restrict a site or method easily.
 
 - The Module_Dog chatbot got some unit tests for the IRC connector and more.

 
## 6.10.4

 - Better performance by only once, and lazily, converting GDT vars to values.
 
 - Timezone detection via Javascript

 - CLI is getting somewhere (but more on TODO)
  
 - JQuery error dialogs
 

## 6.10.3
 
 - New config GDO_SESS_LOCK to globally toggle session locking capabilities. Requires a database. Method has isLockingSession(). By default every Method returns true when a POST request ist made. 
 
 - Big performance and memory usage enhancement.
 
 - Now only 1 global GDT_Response exists. Nested views work by nulling the global instance on rendering, causing a new global instance to be created.
 
 - Cache can now have an expire time.
 
 - New thrid cache for rendered responses on the filesystem (soon).
 
 - New GDO_ERROR_TIMEZONE option for the Logger.
 
 - GDO_MEMCACHE_PREFIX has been removed. The prefix is now version dependant.
 

## 6.10.2

 - Dropped the auto generated name column for performance reasons. Name is optional now. GDT can have a defaultName().
 
 - JSON rendering has breaking changes and makes sense now.
 
 - HTML classnames have been cleaned up.
 
 - GWF_ prefixed config defines are now prefixed GDO_. Try `php gwf2gdo.php`

