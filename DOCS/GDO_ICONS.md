# About Icons

Icons are an important part of your application.
In GDO, there is one icon provider, with the default provider as fallback.
The default provider is GDO/UI/GDT_IconUTF8 which is using utf8 emoticons to provivde icons.

## Render

Create (and render) an icon via gdt pipeline.

    GDT_Icon::make()->icon('arrow_right')->renderCell()
    
Render a static icon.

    echo GDT_Icon::iconS('arrow-right')
        
## Icon providers

There are currently the following icon providers.

 - GDO/UI/GDT_IconUTF8 is the default and fallback provider.
 - GDO/FontAwesome/FA_Icon adds font awesome to your project.
 - GDO/Material/GDT_MaterialIcon for angular material projects.
 - GDO/JQueryMobile/GDT_IconJQM for jquery mobile projects.
 