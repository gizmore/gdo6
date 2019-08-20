# Package management in GDO6

gdo6 does NOT use composer for package management. Instead you use git to install and update gdo6 modules.
gdo6 is NOT PSR-4 compliant. It has an own minimal and optimized autoloader which is maybe 4 lines of code.

gdo6 has the ability to exchange modules for another. An example is Captcha; You clone one of the captcha modules as GDO/Captcha and chose your captcha implementation that way.


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
    
That's it. Your application now includes jquery assets :)
 