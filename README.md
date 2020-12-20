# GDO6

## Gizmore Data Objects 6

GDO6 is a PHP framework to ease the development of websites.
The main audience is highly dynamic websites like games.
All code and modules are currently written by myself, ensuring the same code style and quality.

The main difference to other frameworks is the type system; All other frameworks have multiple type systems; one for DB, one for Models and maybe even more.

In GDO6 the same type system (GDT) applies to all widgets, like tables, forms and also the DB layer.
Write a GDT once, write the validation once, and use the same GDType everywhere in your code.
There are types like Email, Username, Geoposition, Country, Object, File, Image, Enum, Duration, Serialize, ComboBox, Date, CreatedBy and many more.

The performance is quite okay with responsive timing and about 5MB memory footprint with 100+ modules loaded.


## Why?

I was unhappy with RubyOnRails and recoded my framework from scratch. I took the best ideas from all my experience to create the GDT - GizmoreDataType - System, Which i think is missing on all frameworks i have seen so far.

Read https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_AND_GDT.md to learn more about GDT.
 

## Outstanding features

- Two-Layer Single-Identity Cache: Read https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_CACHING.md to learn more about what single identity means.

- Code-First DBA: You write Entities easily with GDT and code completion. Your Database is created with all appropiate foreign keys and perfect relations.

- Unified type system: Use GDT everywhere like in DBA, Forms, Tables and Views. There are more than 50 Different GDT which know how to behave in every context.

- Method Re-Use: Write methods once and make them work for ajax, json, html or re-use them with websockets.

- Code Style: Almost every class is less than 250 lines. No scrolling to the right needed. Clean code!

- Code Completion: In the EclipsePDT IDE the code completion coverage is about 92%. Other IDE are not tested yet.

- PHP Warnings: The code does not produce any warning or notice. By default a notice or warning is treated as application crash and issues an error mail.


## Notable features

 - Much code and logic was ported from my old framework, which already had thoughtful processes. For example the registration does not waste mails or nicknames until activated.

 - The Web Event System can call hooks in the websocket process.

 - No 3rd party template engine: I wrote an own minimal template engine which is just using require. Language specific templates are supported. The GDT_Template can also be used as a normal GDT.
 
 - Yarn: Some modules come with javascript dependencies. Install all of them easily via ./gdo_yarn.sh
 
 - JS: There is a JS minifier that builds a single .js out of all js assets on the fly. Protect your JS code with that uglyfier via setting an option. (TODO: deny access to the src files :O (ouch)
 
 - WYSIWYG: GDT_Message allows html to be typed by users. HTMLPurifier is used to securely allow that. The module gdo6-tinymce adds a nice editor on top of that.

 - Logger: The logger writes logfiles for each user individually.
 
 - Modular: Plug your site together by cloning modules into the GDO/ folder. Some Modules like Session or Captcha exist in different versions. For example session can be stored serverwide via db or on the clients via AES encryption. The crypto is untested. Multiple Captcha module providers are planned. Want a roboto font or font awesome icons, just clone and install the module and the assets are there in source and build. There are two modules to extend GDT_Message editing; CKEditor and tinyMCE.
 
 - DBA layer is nicely handwritten in about 2,500 lines of code(LOC). Keep in mind it replaces sophisticated DBA like activerecord or PDO. It works only with MariaDB at the moment, and this is not planned to be changed. The GDO-DBA is doing a nice job in performance and readability. The single-identity, 2-layer-cached engine is quite fun to use and easy to learn. What is missing is stuff like "has_many", "belongs_to", "etc.". The foreign key usage and management is excellent though, combined primary keys and foreign keys are supported.
 
 - Want some config in your module? Just return an array of GDT in your GDO_Module::getConfig(). That's it. With one line you get all the validation and editing comfort of any GDT, from which there are over 100 widgets now.
 
 - Easily create search forms that search the whole db or whole tables for a filter value. Multisorting is supported.
 
 - Can't access the webserver yet? Run any GDO command and manage your installation via CLI. Just php gdo.php install Captcha and you are ready to go.
 

## Missing/Questionable features

 - The dependency management is not that great yet. There is a mapping missing between modulenames and gdo6-module repository. (Damn). On the other hand the GDO/ cloning strategy works out very well.

 - The DB-Free, "simple single static sites" use case is not supported yet. :(

 - Currently there are only very few Unit Tests. My additional strategy is to issue error mails on any little error, notice or warning. (Some Test are being written. Methods have defined GDT[] gdoParamters() which can be even fuzzed with a couple of lines)

 - Lack of quality design. Currently there is one fairly ugly classic design, a bootstrap theme, a jquerymobile theme and an angular material design. If you are into html/css/design please have a look 

 - The I18n is not that great. Pluralization, for example, is not supported.
 
 - Currently there are no SEO friendly urls. This is on TODO! (Still far away todo)
 
 - Some modules are not that mature yet. For example the forum module is missing lots of design and features like unread threads or search highlight.
 
 - There is only one configuration file. Rest of config is in modules. It would be cool if ENV could override module and config settings
 
 - There is no CMS module and i think i don't want one. I create site via code, not via click. (Module_Blog is planned though)


## Demo

A demo site with material design and almost all modules is available here: http://gdo6.gizmore.org

A demo site with classic design and almost all modules is (SOON) available here: http://classic.gdo6.gizmore.org (NOT YET)

A demo site with bootstrap4 theme is available here: http://mettwitze.gizmore.org


## Installation

The installation process is documented in https://github.com/gizmore/gdo6/blob/master/DOCS/INSTALL.md

There is also a list of official modules to install.


## Security

Security is a big concern and i do my best to provide secure code, elegant processes and a transparent ecosystem.

GDO6 is one of the very few frameworks that offer GPG encrypted mails and alert you on login failures.


### Write own modules

There is a tutorial in the gdo6-helpdesk module, which is worth a read.

https://github.com/gizmore/gdo6-helpdesk/blob/master/howto/HOWTO.md


### Known Bugs / TODO

There is a ROADMAP.md in the DOCS folder


### Contribution

Contribution is welcome via suggestions or testing. Maybe a few bugfixes.
You are welcome to write own modules which i might list as official.


### License

GDO6 is licensed under the MIT license. Mostly all modules are MIT as well. There are only a very few properitary modules, own sites, which might be available on github.
