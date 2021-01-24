# GDO6

A modern approach to the 're-inventing the framework wheel' game. I chose PHP because development is quite fast there. Also with type hinting the code completion is almost charmy and it's quite a fast language.


## Gizmore Data Objects 6.10

GDO6 v6.10 is a PHP framework to ease the development of websites.
The main audience is highly dynamic websites like games.
All code and modules are currently written by myself, ensuring the same code style and "quality".

The main difference to other frameworks is the type system; All other frameworks have multiple type systems; one for DB, one for Models and maybe even more.

In GDO6, the same type system, GDT, applies to all widgets, may it be tables, forms or the DB layer.
Finally DB datatypes like Geoposition, Email, Password, Decimal, Object, Join, Country, Object, File, Image, Enum, Duration, Serialize, Combobox, Username, DateTime, CreatedBy, AutoInc and many more.
Every GDT works in all aspects of the site.
May it be the Responses, HTML tables, PDF-Documents(PLANNED), Database, Forms, or BinaryWebsocketProtocol(GWS).
You can also plug a DB gdt in another and re-use it cleverly to reduce allocations.

Validation is an annoying topic in writing applications.
GDT write their validations once and use them everywhere.
I cannot remember the last time i had to write validation code.
In exchange the GDT System is still a bit tricky to use and program. It's easy to get lost in the code.

The performance is quite okay with responsive timing from around 60ms for complex pages to 30ms for simple pages like a big dynamic sidebar with a contact form.
On a small vserver there is around a 6MB memory footprint running gdo6 having 108 modules loaded on x86_64 using PHP-7.4.0.
Numbers could be completely off though, because, hey it's PHP.

The system is very modular, for example there are currently two message editors available; tinyMCE and CKEditor.
Of course without such a module you will get a simple textarea.
The software design allows to choose or even combine multiple javascript frameworks like jQueryUI, JQueryMobile, AngularMaterial and Bootstrap4.


## Why?

I was unhappy with RubyOnRails and recoded my framework from scratch. I took the best ideas from all my experience to create the GDT - GizmoreDataType - System, Which i think is missing on all frameworks i have seen so far.

Read https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_AND_GDT.md to learn more about GDT.
 

## Outstanding features

[x] Two-Layer Single-Identity Cache: Read https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_CACHING.md to learn more about what "single identity" means.

[x] Code-First DBA: You write Entities easily with GDT and code completion. Your Database is created with all appropiate foreign keys and perfect relations.

[x] Unified type system: Use GDT everywhere like in DBA, Forms, Tables and Views. There are more than 100 different GDT which know how to behave in every context.

[x] Method Re-Use: Write methods once and make them work for ajax, json, html or re-use them with websockets.

[x] Code Style: Almost every class is less than 250 lines. Mostly no scrolling to the right needed. Mostly clean code.

[x] Code Completion: In the Eclipse-PDT IDE the code completion coverage is around 90%. Other IDE are not tested yet.

[x] PHP Warnings: The code does not produce any warning or notice. By default a notice or warning is treated as an application crash which issues an error mail. Warnings in the sourcecode are not that much, compared to all what i have seen on the market.
 
[x] GDO6 has the "everything a string" phylosphy. Means we're getting strings out of the DB, the $_REQUEST and from files. so use strings everywhere possible.

## Notable features

 - Much code and logic was ported from my old framework, which already had thoughtful processes. For example the registration does not waste mails or nicknames until activated.

 - The Web Event System can call hooks in the websocket process.

 - No 3rd party template engine: I wrote an own minimal template engine which is just using require. Language specific templates are supported. The GDT_Template can also be used as a normal GDT.
 
 - Yarn: Some modules come with javascript dependencies. Install all of them easily via ./gdo_yarn.sh
 
 - JS: There is a JS minifier that builds a single .js out of all js assets on the fly. Protect your JS code with that uglyfier via setting an option. (TODO: Protect the source js files)
 
 - WYSIWYG: GDT_Message allows HTML to be typed by users. HTMLPurifier is used to securely allow that. The module gdo6-tinymce adds a nice editor on top of that. Meanwhile CKEditor is available too, but most people would probably want a Markdown module. It's on todo.

 - Logger: The logger writes logfiles for each user individually.
 
 - Modular: Plug your site together by cloning modules into the GDO/ folder. Some Modules like Session or Captcha exist in different versions. For example session can be stored serverwide via db or on the clients via AES encryption. The crypto is untested. Multiple Captcha module providers are planned. Want a roboto font or font awesome icons, just clone and install the module and the assets are there in source and build. There are two modules to extend GDT_Message editing; CKEditor and tinyMCE.
 
 - DBA layer is nicely handwritten in about 2,500 lines of code(LOC). Keep in mind it replaces sophisticated DBA like ActiveRecord or PDO. It works only with MariaDB at the moment, and this is not planned to be changed. The GDO-DBA is doing a nice job in performance and readability. The single-identity, 2-layer-cached engine is quite fun to use and easy to learn. What is missing is stuff like "has_many", "belongs_to", "etc.". The foreign key usage and management is excellent though, combined primary keys and foreign keys are well supported.
 
 - Want some config in your module? Just return an array of GDT in your GDO_Module::getConfig(). That's it. With one line you get all the validation and editing comfort of any GDT, from which there are over 100 widgets now.
 
 - Easily create search forms that search the whole db or whole tables for a filter value. Multisorting is supported.
 
 - Can't access the webserver yet? Run any GDO command and manage your installation via CLI. Just php gdo.php install Captcha and you are ready to go.
 
 You can also use it as swiss army knife: php gdo.php call News Write '{"en" => {"title" => "Trying out gdo6!", "message" => "This news item was created via CLI" }}'
 

## Missing/Questionable features

 - The dependency management is not that great yet. There is now at least a mapping from module names to repositories which will be used soon to aid in the installation process.

 - The DB-Free, "simple single static sites" use case is not supported yet. Maybe it's easier than i think.

 - Currently there are only very few Unit Tests. My additional strategy is to issue error mails on any little error, notice or warning. Some Test are being written and some stuff can be trivially tested. Methods without parameters should throw a 200. Methods should have defined GDT[] gdoParamters() which could even be fuzzed with a couple of lines.

 - Lack of quality design. Currently there is one fairly ugly classic design, a bootstrap theme, a jquerymobile theme and an angular material design. If you are into html/css/design please have a look. Maybe Ionic is next? 

 - The I18n is not that great. Pluralization, for example, is not supported. should still be a nice and fast replacement. Keys are re-used over module boundaries. Dependency hell is better than too much to translate?
 
 - Some modules are not that mature yet. For example the forum module is missing lots of design and features like unread threads or search highlight.
 
 - There is no CMS module and i think i don't want one. I create site via code, not via click. (Module_Blog is planned though)

- SEO is still a big topic i would need to discuss. I don't yet have a good idea how to make SEO friendly urls. Params are a huge mess for tables and lists :/

 - Search a response deeply for a var that mactches .Similiar to searchGDO() but for GDTs: searchResponse.

## Demo

A demo site with material design is available here: https://geo2country.gizmore.org

A demo site with bootstrap4 theme is available here: https://mettwitze.gizmore.org

A demo site with jQueryMobile theme is available here: https://service.busch-peine.de

A demo site with classic theme is available here: https://ranzgruppe.com 


## Installation

The installation process is documented in https://github.com/gizmore/gdo6/blob/master/DOCS/INSTALL.md
There is also a list of official modules to install.
Meanwhile there is GDO/Core/ModuleProviders.php which holds a list of official gdo6 modules to aid in the installation process.


## Security

Security is a big concern and i do my best to provide secure code, elegant processes and a transparent ecosystem. GDO6 offers GPG encrypted mails and alerts you on login failures or provider changes.


### Write own modules

There is a tutorial in the gdo6-helpdesk module, which is worth a read.

https://github.com/gizmore/gdo6-helpdesk/blob/master/howto/HOWTO.md


### Known Bugs / TODO

There is a ROADMAP.md in the DOCS folder


### Contribution

Contribution is welcome via suggestions or testing. Maybe a few bugfixes.
You are welcome to write own modules which i might list as official.


### License

The GDO6 Core is licensed under the MIT license. Mostly all modules are MIT as well. There are only a very few properitary modules, own sites, which might be available on github for the interested reader.

