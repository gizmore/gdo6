# gdo6 WWW installation

To install gdo6 via the web interface follow this document.
There is also help for [apache configuration](GDO_AND_APACHE2.md) and [nginx setup](GDO_AND_NGINX.md).


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
    
## The install wizard

(to be done)
