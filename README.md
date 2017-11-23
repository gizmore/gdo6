# gdo6

Gizmore Data Objects 6


## Features

- allows to build sites without a database or install.
- module-only, means that there every piece of code is contained in a module.
- Code first dba
- Two layer single identity cache.


## Demo

A demo site with (almost) the module set from below is here: http://gdo6.gizmore.org


### Modules

For a "quick" install.

1. Install core:

    mkdir www && cd www
    git clone --recursive https://github.com/gizmore/gdo6


In case you forgot a recursive:

    git submodule update --init --recursive



Install modules:

Switch to the GDO folder and clone or code submodules.


It is a good strategy to paste the below clone lines to a text file.
Then remove the modules you do not want and clone the remaining modules.

Modules with ## are sites/projects, you probably don't want those.
Modules with # are not ready for production yet.

Be a bit careful with mixing theme/css modules like JQueryUI, Bootstrap and Material.
The material design is tricky to setup atm and not recommended yet.


    cd www/gdo6/GDO # switch to modules dir

    git clone --recursive https://github.com/gizmore/gdo6-account Account
    git clone --recursive https://github.com/gizmore/gdo6-address Address
    git clone --recursive https://github.com/gizmore/gdo6-admin Admin
    # git clone --recursive https://github.com/gizmore/gdo6-angular Angular
    git clone --recursive https://github.com/gizmore/gdo6-audio Audio
    git clone --recursive https://github.com/gizmore/gdo6-avatar Avatar
    git clone --recursive https://github.com/gizmore/gdo6-backup Backup
    ## git clone --recursive https://github.com/gizmore/gdo6-backup Blog
    git clone --recursive https://github.com/gizmore/gdo6-bootstrap Bootstrap
    ## git clone --recursive https://github.com/gizmore/gdo6-buzzerapp Buzzerapp
    git clone --recursive https://github.com/gizmore/gdo6-captcha Captcha
    git clone --recursive https://github.com/gizmore/gdo6-category Category
    git clone --recursive https://github.com/gizmore/gdo6-comment Comment
    git clone --recursive https://github.com/gizmore/gdo6-contact Contact
    git clone --recursive https://github.com/gizmore/gdo6-cors CORS
    git clone --recursive https://github.com/gizmore/gdo6-country-coordinates CountryCoordinates
    git clone --recursive https://github.com/gizmore/gdo6-currency Currency
    ## git clone --recursive https://github.com/gizmore/gdo6-dog Dog
    git clone --recursive https://github.com/gizmore/gdo6-download Download
    git clone --recursive https://github.com/gizmore/gdo6-facebook Facebook
    git clone --recursive https://github.com/gizmore/gdo6-font-awesome FontAwesome
    git clone --recursive https://github.com/gizmore/gdo6-forum Forum
    git clone --recursive https://github.com/gizmore/gdo6-friends Friends
    git clone --recursive https://github.com/gizmore/gdo6-gallery Gallery
    git clone --recursive https://github.com/gizmore/gdo6-geo2country Geo2Country
    # git clone --recursive https://github.com/gizmore/gdo6-guestbook Guestbook
    # git clone --recursive https://github.com/gizmore/gdo6-helpdesk Helpdesk
    git clone --recursive https://github.com/gizmore/gdo6-import-gwf3 ImportGWF3
    git clone --recursive https://github.com/gizmore/gdo6-ip2country IP2Country
    git clone --recursive https://github.com/gizmore/gdo6-jquery JQuery
    git clone --recursive https://github.com/gizmore/gdo6-jquery-ui JQueryUI
    git clone --recursive https://github.com/gizmore/gdo6-links Links
    git clone --recursive https://github.com/gizmore/gdo6-login Login
    git clone --recursive https://github.com/gizmore/gdo6-logs Logs
    git clone --recursive https://github.com/gizmore/gdo6-maps Maps
    # git clone --recursive https://github.com/gizmore/gdo6-material Material
    ## git clone --recursive https://github.com/gizmore/gdo6-nasdax Nasdax
    git clone --recursive https://github.com/gizmore/gdo6-news News
    git clone --recursive https://github.com/gizmore/gdo6-online-users OnlineUsers
    # git clone --recursive https://github.com/gizmore/gdo6-opentimes OpenTimes
    git clone --recursive https://github.com/gizmore/gdo6-payment Payment
    git clone --recursive https://github.com/gizmore/gdo6-payment-credits PaymentCredits
    git clone --recursive https://github.com/gizmore/gdo6-payment-paypal PaymentPaypal
    git clone --recursive https://github.com/gizmore/gdo6-pm PM
    git clone --recursive https://github.com/gizmore/gdo6-poll Poll
    git clone --recursive https://github.com/gizmore/gdo6-profile Profile
    git clone --recursive https://github.com/gizmore/gdo6-push Push
    git clone --recursive https://github.com/gizmore/gdo6-recovery Recovery
    git clone --recursive https://github.com/gizmore/gdo6-register Register
    # git clone --recursive https://github.com/gizmore/gdo6-shoutbox Shoutbox
    git clone --recursive https://github.com/gizmore/gdo6-tag Tag
    git clone --recursive https://github.com/gizmore/gdo6-tinymce TinyMCE
    # git clone --recursive https://github.com/gizmore/gdo6-usergroup Usergroup
    git clone --recursive https://github.com/gizmore/gdo6-vote Vote
    git clone --recursive https://github.com/gizmore/gdo6-websocket Websocket
    ## git clone --recursive https://github.com/gizmore/gdo6-wechall WeChall
    ## git clone --recursive https://github.com/gizmore/gdo6-wombat Wombat
    
Then make your webserver point to the gdo6 directory and request install/wizard.php in your browser.

Do not forget the bower/js stuff.

    
### Write own modules

There is a tutorial in the gdo6-helpdesk module, which is worth a read.

https://github.com/gizmore/gdo6-helpdesk/blob/master/howto/HOWTO.md


There is a tutorial section in gdo6/tutorial.

### Known Bugs / TODO

There is a ROADMAP.md
