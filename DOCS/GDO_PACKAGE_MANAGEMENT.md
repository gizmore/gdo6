# Package management in GDO6

gdo6 does NOT use composer for package management. Instead you use git to install and update gdo6 modules.

gdo6 is NOT PSR-4 compliant. It has an own minimal and optimized autoloader. Although it is similiar to PSR-4 it is not compatible, yet.

gdo6 has the ability to exchange modules for another. An example is Captcha; You clone one of the captcha modules as GDO/Captcha and have chosen your captcha implementation that way. This seems not easily possible with PSR-4, or is it?


## Installing a gdo6 module

The installation process for a gdo6 module is as follows.

1) Clone the module into your GDO folder with the correct foldername. The correct foldername is important. 

    cd gdo6/GDO # go to module folder
    git clone --recursive https://github.com/gizmore/gdo6-jquery JQuery # clone this module as JQuery
    cd gdo6 # go to root folder
    ./gdo_yarn.sh # Install all js dependencies
    
2) Enable the module by installing it via admin panel.

    # Login as admin
    # Goto Admin->Modules->JQuery
    # Click Install
    
2b) Enable the module by using ./gdo.sh

    ./gdo.sh install JQuery
    
That's it. Your application now includes jquery assets :)

 
## Updating your installation

Updating all your GDO6 modules can be easily done using the following command.

    cd gdo6
    ./gdo_update.sh
    
This will run git pull on the core and all modules.

It might be that you have to run install on some modules after update. This can be done via the installer or the admin panel.

The developers must assure that all modules stay backwards compatible!
