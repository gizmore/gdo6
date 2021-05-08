# GDO6 Theming

GDO6 is very flexible when it comes to theming.

Your theme is defined in protected/config.php like this.

    define('GDO_THEME', 'jqui,classic,default');
    

This will instruct the template engine to look for templates in jqui first, then classic, and at last the default theme.

Default is not a real theme, it is just the default templates that ship with all the GDT. Make sure you _always_ have default as the last theme in your config.

Some modules ship with a theme, like the modules _Material_, _Classic_, _BootstrapTheme_ or _JQueryUI_. The BootstrapTheme theme is recommended at the moment.


## Creating a new theme

Create a new module and return a theme name.

    class MyModule extends GDO_Module
    {
        public function getThemes() { return ['mytheme']; }
    }
    

After the module is installed you can change the config.

    define('GDO_THEME', 'mytheme,classic,default');
    

As an example we will change the impressum from the Core module. In the Core module this template is located at GDO/Core/tpl/page/impressum.php.

Create a new file in your module with this path. 

     GDO/MyModule/thm/mytheme/Core/tpl/page/impressum.php
     

Voila, you have succesfully overwritten a template.


## GDO6 Templates

GDO6 templates are simple php files. It is recommended to NOT output any HTML or text in view templates. Instead use GDT so the template supports all content types and/or websocket invocation. Some PHP logic is used in some templates, so the behaviour can be altered via theming which grants great flexibility in a MVVM style.

GDO6 templates support locale aware loading. For example you can create impressum_en.php and impressum_de.php.

### Template API

Templates are handled by the GDT_Template implementation.
You can call the static php function or add a real GDT to the fields you work with.

Render a template:

    echo GDT_Template::php('MyModule', 'page/mypage.php', ['test' => 'foobar']);
    
Use GDT_Template as a GDT to be added to forms, tables, lists, etc.:

    $gdt = GDT_Template::make()->template('MyModule', 'page/mypage.php', ['test' => 'foobar']);
