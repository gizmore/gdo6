# Why gdo6 and not any of the frameworks out there?

I am just unhappy with all other design decisions so far.
Admitted, i only tried a few frameworks, and tell about my experience with them here, compared to gdo6.


## Why not GWF3

It's really awful to code in it. The performance is quite nice, and also SEO is not totally bad, but coding with it is cumbersome. Methods with lots of parameters that are hard to get right when only a late parameter is needed.
Some bad conceptions when it comes to forms and tables.
It serves www.wechall.net
I18n is not professional.
Lot of repetitive code to write
Not very powerful.


## Why not RubyOnRails

Ruby is way more slow compared to PHP by it's way more OOP and dynamical nature. In Ruby i loved to type class decoraters to write code that writes code. Awesome!
ActiveRecord is very complex and not easy to learn.
I have written an ActiveRecord decorator that adds gdo6 style of cache to ActiveRecord: (https://github.com/gizmore/ricer2/blob/master/config/initializers/ricer/curry.rb#L45)[ricer2 chatbot, curry.rb] but it is far from the state gdo6 has achieved. 
Writing validators is totally annoying and you have to write the same validator twice, one time in JS and one time in Ruby.
The CI Pipeline is probably OK when i remember it right.
Deploying Rails apps can be tricky, when gems get updated.
It is a total mood killer on Windows to have a space in your mysql path when building mysqli gem from scratch. Required back then.


## Why not Wordpress

OMG the best framework ever! The spagetthi code is easy to maintain and it only requires 10MB of RAM for bootstrapping a blank page.
Admitted, the last time i took a look at wordpress is about ten years ago. I am sure it is even more backwards compatible now :)
Thanks to the blazing fast routing, you can have over 1000 different urls until any db config would choke because the serialized hashmap is updated in a single query.
Many modules!
The iterative attempt in displaying content looked nice but felt wrong.


## Why not Joomla!

Slow as fuck. Updates often break things.
Totally crazy MVVC.
XML config hell.
Takes ages to develop for it.
Would not buy again.


## Why not JSF

OMG! Java... really? Bloat and config hell.


## Why not Laravel8

The config env is buggy and gave me headache a few times.
Validators have to be implemented multiple times and are not nice to write.
Laravel tries hard to be like rails.
Eloquent does not have a single identity cache.
Asset pipeline is not intuitive.
Quite some duplicate code you have to write.
Deployments are huge thanks to huge vendor folder.
Not too bad framework after all.
Blaze is actually a nice template engine, but you cannot debug templates.
Migrations are not as nice to write as GDO->gdoColumns()
Very flexible and solid!


## Why not devextreme

This is a javascript framework and there is even a gdo6 theme.
Warning! Real, Config, Hell!
Slow.
Costy.


## Why not gdo6

Most modules are not 100% finished yet.
Composition in forms is not really working.
Custom form layouts are not supported / needed?
It is a bit slow as it chucks together a lot of GDT and GDO.
There is only a single developer atm.
You don't have to learn a new template language.
I18n is not very professional.
Pluralization is missing.
Too many caches?
Not using MVC! =) It is using MTT - Model Type Template
