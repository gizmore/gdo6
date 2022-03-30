# GDO Module Creation

## More examples

There is a [howto](https://github.com/gizmore/gdo6-helpdesk/blob/master/howto/HOWTO.md) in [Module_Helpdesk](https://github.com/gizmore/gdo6-helpdesk)


## Naming conventions

For gdo to find your classes we have a few naming conventions.

    # GDO/MyModule/Module_MyModule.php 
    namespace GDO\MyModule;
    class Module_MyModule extends GDO_Module


## Adding it to official modules

Add to [GDO_MODULES.md](./GDO_MODULES.md) and [INSTALL.md](INSTALL.md).
Add to [ModuleProviders.php](../GDO/Core/ModuleProviders.php) (generate via a shell script in root)
