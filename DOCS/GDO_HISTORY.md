# History

gdo6 Changelog.


## Install today!

    git clone --recursive https://github.com/gizmore/gdo6 && cd gdo6
    ./gdoadm.sh configure
    # create a mysql database.
    ./gdoadm.sh provide DogWebsite
    ./gdoadm.sh install_all
    ./gdo_yarn.sh
    ./gdo_bower.sh
    ./gdoadm.sh admin username password email
    ./gdoadm.sh htaccess
    ./gdoadm.sh cronjob
    ./gdoadm.sh secure
    # Set your PATH env to gdo6/bin
    gdo admin.clearcache. # :)
    # It is also possible to install via http://gdo6/install/wizard.php

## Improvements for v6.11.0 (27.11.2021)
    
 - GDT_CreatedAt, GDT_EditedAt and GDT_DeletedAt are now GDT_Timestamp(6) instead of GDT_DateTime(3).
 
 - GDO now automigrates via DB table copy, drop, create fresh, select copy into original table.

     
## Improvements for v6.10.7 (27.11.2021)

 - GDT_Timezone is now a GDO_Timezone database table for using IDs as timezones

    
## Improvements for v6.10.6 (21.10.2021)

 - SEO friendly urls. The format is /module/method/key/val/key/val/?arrays[]=OrUnder&_score=Params#hash

 - All requests are now routed through index.php

 - Directory Indexing. Easy peasy

 - CSS minifier. Pretty fast 3rd party PHP impl.
 
 - Optionally disallow css and js dev source files
 
 - Bootstrap5 Theme
 
 - GDT_Message Markdown CoDec
 

## v6.10.5 (15.09.2021)

 - Time based GDT, like Date, DateTime and Timestamp now return a \DateTime object in toValue().

 - Module_Birthday allows to age restrict a site or method easily.
 
 - The Module_Dog chatbot got some unit tests for the IRC connector and more.

 
## v6.10.4

 - Better performance by only once, and lazily, converting GDT vars to values.
 
 - Timezone detection via Javascript

 - CLI is getting somewhere (but more on TODO)
  
 - JQuery error dialogs
 

## v6.10.3
 
 - New config GDO_SESS_LOCK to globally toggle session locking capabilities. Requires a database. Method has isLockingSession(). By default every Method returns true when a POST request ist made. 
 
 - Big performance and memory usage enhancement.
 
 - Now only 1 global GDT_Response exists. Nested views work by nulling the global instance on rendering, causing a new global instance to be created.
 
 - Cache can now have an expire time.
 
 - New thrid cache for rendered responses on the filesystem.
 
 - New GDO_ERROR_TIMEZONE option for the Logger.
 
 - GDO_MEMCACHE_PREFIX has been removed. The prefix is now version and domain dependant.
 

## 6.10.2

 - Dropped the auto generated name column for performance reasons. Name is optional now. GDT can have a defaultName(). Unnamed GDT use a numeric array index.
 
 - JSON rendering has breaking changes and makes sense now.
 
 - HTML classnames have been cleaned up.
 
 - GWF_ prefixed config defines are now prefixed GDO_. Try `php gwf2gdo.php`

