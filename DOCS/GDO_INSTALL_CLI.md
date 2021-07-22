# GDO6 CLI installation

To install gdo6 via, and for, the CLI, follow this document.
To install gdo6 via the web interface follow [GDO_INSTALL_WWW.md].


## Prerequisites

It is recommended to have nodejs, npm, yarn and bower installed.
Bower support and dependency will be dropped sooner or later.

    npm -g install yarn
    npm -g install bower


## Install the gdo6 core

    git clone --recursive https://github.com/gizmore/gdo6
    cd gdo6
    ./gdo_yarn.sh
    ./gdo_bower.sh


## Configure the gdo6 system

Please configure your system by creating a config at the protected/ folder.

    ./gdoadm.sh configure # Create a default config. Please edit it.


## PATH

You can add gdo6/bin/ to your PATH environment, so you can do fancy stuff like this.
    
    gdo core.impressum. # prints method Core/Impressum to the CLI
    gdo register.form --tos=1 gizmore gizmore@gizmore.org password password # Register at your gdo6 installation via CLI
    
    
## Cronjob

It is recommended to install cronjobs at your system running gdo6.
Cronjobs, for example, clean guest users and sessions or send mails on PM/Forum activity etc. 

    ./gdoadm.sh cronjob # Print cronjob instructions. Apply them to your system.
   
    
## Security

You want some folders to be inaccessible from the outside. Like the install wizard or your protected/ folder, files/ folder, temp/ folder etc.

    ./gdoadm.sh secure
    

## Build your site

You can now clone more modules and install them.
Here is an example on how to create the TBS website.

    ./gdoadm.sh provide TBS
    ./gdoadm.sh install TBS
    ./gdo_yarn.sh
    ./gdo_bower.sh
    
    
## Admin account

Of course you want an admin account for your installation.

    ./gdoadm.sh admin username password <email>
        