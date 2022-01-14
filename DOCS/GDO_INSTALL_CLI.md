# gdo6 CLI installation

To install gdo6 via, and for, the CLI, follow this document.
To install gdo6 via the install/wizard.php web interface follow GDO_INSTALL_WWW.md - coming soon.


## Install gdo6 today!

Maybe implement something from the [GDO_TODO.md](https://github.com/gizmore/gdo6/blob/master/DOCS/GDO_TODO.md) list.


## Prerequisites

 - You *have to* install PHP. It might run fine on 6.? Surely 7.0 and 8.1 is latest and recommended.

 - You *have to* install a mariadb compatible database.

 - You *have to* install git. See [git4windows](https://git-scm.com/download/win) or [git](https://github.com/git/git)
 
It is recommended to have nodejs, npm, yarn and bower installed.
Bower will be dropped sooner or later.

    npm -g install yarn
    npm -g install bower


## Install the gdo6 core

The `--recursive` option has to be used.

    git clone --recursive https://github.com/gizmore/gdo6
    cd gdo6
    ./gdo_yarn.sh
    ./gdo_bower.sh
    
    
## gdoadm.sh

You will work with the gdoadm CLI tool.

    ./gdoadm.sh # linux style
    php gdoadm.php # windows
    
    
## Run the system test

    ./gdoadm.sh systemtest


## Configure the gdo6 system

Please configure your system by creating a config at the protected/ folder.

    ./gdoadm.sh configure # Create a default config. Please edit it.


## Download modules

    ./gdoadm.sh modules # show modules
    ./gdoadm.sh provide <module> # download module
    ./gdoadm.sh install <module> # install module
    
    
## Install full dev suite

    composer update
    ./gdoadm.sh provide_all
    ./gdoadm.sh install_all
    

## PATH

You can add gdo6/bin/ to your PATH environment, so you can do fancy stuff like this.
    
    gdo core.impressum. # prints method GDO/Core/Method/Impressum to the CLI
    
Send a mail to gizmore via the CLI.

    gdo mail.send gizmore "Hi there" "Mail body goes here."
    
Register at your gdo6 installation via CLI. This happens on your first command.

    gdo core.whoami. # print your gdo_user.user_name
    
    
## Cronjob

It is recommended to install cronjobs at your system running gdo6.
Cronjobs, for example, clean guest users and sessions or send mails on PM/Forum activity etc. 

    ./gdoadm.sh cronjob # Print cronjob instructions. Apply them to your system.
   
    
## Security

You want some folders to be inaccessible from the outside. Like the install wizard or your protected/ folder, files/ folder, temp/ folder etc.

    ./gdoadm.sh secure
    

## Admin account

Of course you want an admin account for your installation.

    ./gdoadm.sh admin username password <email>


## Build your site

You can now clone more modules and install them.
Here is an example on how to create the TBS website.

    ./gdoadm.sh provide TBS
    ./gdoadm.sh install TBS
    ./gdo_yarn.sh
    ./gdo_bower.sh
    
    
## Install all modules for a complete unit test  and development environment

    ./gdo_adm.sh provide_all
    ./gdo_adm.sh install_all
    ./gdo_yarn.sh
    ./gdo_bower.sh
    composer update
    ./gdo_test.sh

