# Why gdo6 and not any of the frameworks out there?

I am just unhappy with all other design decisions so far.
Admitted, i only tried a few frameworks, and tell about my experience with them here, compared to gdo6.


## Why not GWF3

It's really awful to code in it. The performance is quite nice, and also SEO is not totally bad, but coding with it is cumbersome. Methods with lots of parameters that are hard to get right when only a late parameter is needed.


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
Admitted, the last time i took a look at wordpress is ten about ten years ago. I am sure it is even more backwards compatible now :)
Thanks to the blazing fast routing, you can have over 1000 different urls until any db config would choke because the serialized hashmap is updated in a single query.
Many modules!
The iterative attempt in displaying content looked nice but felt wrong.

## Why not Joomla!


## Why not JSF

