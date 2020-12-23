# GDO6 Philosophy and code guideline

- Everything is a string. You get strings out of the db, out of $_REQUEST vars and memcached... literally everything is a string at the beginning. Because of that, gdo6 does not convert numeric data to numbers in the first place. This should give some decent performance stats.
