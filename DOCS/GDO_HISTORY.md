# gdo6 History

Before you read the history, please take your time to actually [install gdo6](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_INSTALL_CLI.md).

## Improvements for v6.11.3 (t.b.a.)

 - New [GDT_Classname](https://github.com/gizmore/gdo6/tree/master/GDO/DB/GDT_Classname.php) to reflect a classname variable. Simliar to a GDT_Name, but longer and with a different string pattern.
 
 - New `gdoadm.sh migrate <module>` and `migrate_all` to force run auto-migrations. This can also be triggered via reinstall in the www admin panel.


## Improvements for v6.11.2 (23.12.2021)

 - New [Javascript](https://github.com/gizmore/gdo6/tree/master/GDO/Javascript) [error mails](https://github.com/gizmore/gdo6/blob/master/GDO/Javascript/Method/Error.php) and [error handler](https://github.com/gizmore/gdo6/blob/master/GDO/Javascript/js/gdo6-debug.js) to detect javascript problems on your clients.

 - New [GDT_Object](https://github.com/gizmore/gdo6/blob/master/GDO/DB/GDT_Object.php) attribute [$autojoin](https://github.com/gizmore/gdo6/blob/master/GDO/DB/WithObject.php#L381). Columns like [GDT_DeletedBy](https://github.com/gizmore/gdo6/blob/master/GDO/DB/GDT_DeletedBy.php) are not wanted to automatically join during every select.

 - New date datatypes; [GDT_Week](https://github.com/gizmore/gdo6/blob/master/GDO/Date/GDT_Week.php), [GDT_Month](https://github.com/gizmore/gdo6/blob/master/GDO/Date/GDT_Month.php), [GDT_Quarter](https://github.com/gizmore/gdo6/blob/master/GDO/Date/GDT_Quarter.php), [GDT_Year](https://github.com/gizmore/gdo6/blob/master/GDO/Date/GDT_Year.php). These inherit GDT_Date and snap their DATEs to the beginning of their timespan. Weekstart ist monday! :)
 
 - The int and date table filters now have two fields for min and max. First i tried custom syntax like "!foo" and "4-8" ... This did not work out very well in terms of compatibility with dev-extreme grid integration.
 

## Improvements for v6.11.1 (5.12.2021)

 - The gdo6 default http error response code changed from 405 Method to 409 Conflict. 
 
 - The GDT_Hook system now uses a filecache for the hook table.
 
 - Cronjobs now run in --force mode via the admin panel. cronjobs.sh added the --force parameter.
 

## Improvements for v6.11.0 (29.11.2021)
 
 - *./gdoadm.sh provide_all* - to download all available gdo6 packages. Unit test all the things!
     
 - GDT_CreatedAt, GDT_EditedAt and GDT_DeletedAt are now GDT_Timestamp(3) instead of GDT_DateTime(3).
 
 - GDO now automigrates on minor patch levels via DB table 1.copy, 2.drop, 3.create_fresh, 4.select copy_into_fresh_table.
 
 - GDO->copy helpers for copying GDT are no longer supported. Reuse is a key feature. Might only be a problem for ranzgruppe.com.

 - MethodCronjob methods can now specify the function runAt() to configure when and how often the cronjobs run. Crontab syntax is used. Some old cronjob code still needs to be refactored.
 

## Improvements for v6.10.7 (27.11.2021)

 - GDT_Timezone is now a GDO_Timezone database table for using IDs as timezones
 
 - GDO_TIMEZONE config var has been removed. UTC is the default timezone until user selects one, or autosets via javascript.

    
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

 - CLI is getting somewhere
  
 - JQuery error dialogs
 

## v6.10.3
 
 - New config GDO_SESS_LOCK to globally toggle session locking capabilities. Requires a database. Method has isLockingSession(). By default every Method returns true when a POST request ist made. 
 
 - Big performance and memory usage enhancement.
 
 - Now only 1 global GDT_Response exists. Nested views work by nulling the global instance on rendering, causing a new global instance to be created.
 
 - Caches can now have an expire time.
 
 - New third cache for rendered responses on the filesystem. (GDO_FILECACHE) Also used for a merged lang file.
 
 - New GDO_ERROR_TIMEZONE option for the Logger.
 
 - GDO_MEMCACHE_PREFIX has been removed. The prefix is now version and domain dependant.
 

## 6.10.2

 - Dropped the auto generated name column for performance reasons. Name is optional now. GDT can have a defaultName(). Unnamed GDT use a numeric array index.
 
 - JSON rendering has breaking changes and makes sense now.
 
 - HTML classnames have been cleaned up.
 
 - GWF_ prefixed config defines are now prefixed GDO_. Try `php gwf2gdo.php` (removed)

