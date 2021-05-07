# GDO6 installation process

This document describes the installation process of gdo6.
Basically you start with cloning the core gdo6 and then clone more modules into 'gdo6/GDO/'.
Then you either run the install/wizard.php or use the CLI to install a gdo6 environment.


### Operating System

GDO6 is running fine on windows, linux and BSD.


### PHP Requirements

gdo6(PHP) runs on PHP 7.0 and later versions up to (8.0) and requires a few PHP modules

    apt-get install php-mbstring php-bcmath php-curl php-mysql php-gd

These modules are optional

    apt-get install php-memcached php-gmp


### GDO6 Core

For a "quick" install.

1. Install core:

        mkdir www && cd www
    
        git clone --recursive https://github.com/gizmore/gdo6

In case you forgot a recursive:

    git submodule update --init --recursive


### GDO6 modules

Switch to the GDO folder and clone or code modules.

It is a good strategy to paste the clone lines below to a text file.
Then remove the modules you do not want and clone the remaining modules.

Modules with ## are websites/projects, you probably don't want those but they are good code examples.

Modules with # are not ready for production yet.

Be a bit careful with mixing theme/css modules like JQueryUI, Bootstrap and Material.

All designs have different quality and varying tpl implementation support.
Sorry for that.
If a component is missing in a theme contact me or try a pull request.

### Licensing

Each module is open source and MIT licensed with a very few exceptions.
Some website modules like Dog and WeChall, and maybe a few others, are my property.
Very private stuff is only at own git servers.
Modules have a license property which defaults to [MIT](LICENSE_MIT)

I intend to keep all stuff MIT licensed for as long as possible so one could really do fancy stuff in gdo6.

### Official gdo6 modules

Copy this list and remove / add modules.

    cd www/gdo6/GDO # switch to modules dir

    git clone --recursive https://github.com/gizmore/gdo6-account Account
    git clone --recursive https://github.com/gizmore/gdo6-activation-alert ActivationAlert
    git clone --recursive https://github.com/gizmore/gdo6-address Address
    git clone --recursive https://github.com/gizmore/gdo6-admin Admin
    git clone --recursive https://github.com/gizmore/gdo6-alcoholicers Alcoholicers
    ## git clone --recursive https://github.com/gizmore/gdo6-amphp AmPHP
    # git clone --recursive https://github.com/gizmore/gdo6-angular Angular
    git clone --recursive https://github.com/gizmore/gdo6-audio Audio
    git clone --recursive https://github.com/gizmore/gdo6-avatar Avatar
    git clone --recursive https://github.com/gizmore/gdo6-backup Backup
    # git clone --recursive https://github.com/gizmore/gdo6-bbcode BBCode
    git clone --recursive https://github.com/gizmore/gdo6-birthday Birthday
    ## git clone --recursive https://github.com/gizmore/gdo6-blog Blog
    git clone --recursive https://github.com/gizmore/gdo6-bootstrap Bootstrap
    ## git clone --recursive https://github.com/gizmore/gdo6-bootstrap3 Bootstrap3
    ## git clone --recursive https://github.com/gizmore/gdo6-bootstrap-theme BootstrapTheme
    ## git clone --recursive https://github.com/gizmore/gdo6-buzzerapp Buzzerapp
    git clone --recursive https://github.com/gizmore/gdo6-captcha Captcha
    git clone --recursive https://github.com/gizmore/gdo6-recaptcha2 Captcha
    git clone --recursive https://github.com/gizmore/gdo6-category Category
    ## git clone --recursive https://github.com/gizmore/gdo6-ckeditor CKEditor
    git clone --recursive https://github.com/gizmore/gdo6-comment Comment
    git clone --recursive https://github.com/gizmore/gdo6-contact Contact
    git clone --recursive https://github.com/gizmore/gdo6-cors CORS
    git clone --recursive https://github.com/gizmore/gdo6-country-coordinates CountryCoordinates
    git clone --recursive https://github.com/gizmore/gdo6-currency Currency
    ## git clone --recursive https://github.com/gizmore/gdo6-docs Docs
    ## git clone --recursive https://github.com/gizmore/gdo6-dog Dog
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-auth DogAuth
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-irc DogIRC
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-irc-autologin DogIRCAutologin
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-irc-spider DogIRCSpider
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-shadowdogs DogShadowdogs
    ## git clone --recursive https://github.com/gizmore/gdo6-dog-tick DogTick
    git clone --recursive https://github.com/gizmore/gdo6-download Download
    git clone --recursive https://github.com/gizmore/gdo6-dsgvo DSGVO
    git clone --recursive https://github.com/gizmore/gdo6-facebook Facebook
    git clone --recursive https://github.com/gizmore/gdo6-favicon Favicon
    # git clone --recursive https://github.com/gizmore/gdo6-follower Follower
    git clone --recursive https://github.com/gizmore/gdo6-font-awesome FontAwesome
    git clone --recursive https://github.com/gizmore/gdo6-font-roboto FontRoboto
    git clone --recursive https://github.com/gizmore/gdo6-font-titillium FontTitillium
    git clone --recursive https://github.com/gizmore/gdo6-forum Forum
    git clone --recursive https://github.com/gizmore/gdo6-friends Friends
    git clone --recursive https://github.com/gizmore/gdo6-gallery Gallery
    ## git clone --recursive https://github.com/gizmore/gdo6-geo2country Geo2Country
    # git clone --recursive https://github.com/gizmore/gdo6-google-translate GoogleTranslate
    # git clone --recursive https://github.com/gizmore/gdo6-guestbook Guestbook
    # git clone --recursive https://github.com/gizmore/gdo6-helpdesk Helpdesk
    git clone --recursive https://github.com/gizmore/gdo6-import-gwf3 ImportGWF3
    git clone --recursive https://github.com/gizmore/gdo6-instagram Instagram
    git clone --recursive https://github.com/gizmore/gdo6-invite Invite
    git clone --recursive https://github.com/gizmore/gdo6-ip2country IP2Country
    git clone --recursive https://github.com/gizmore/gdo6-jpgraph JPGraph
    git clone --recursive https://github.com/gizmore/gdo6-jquery JQuery
    git clone --recursive https://github.com/gizmore/gdo6-jquery-autocomplete JQueryAutocomplete
    # git clone --recursive https://github.com/gizmore/gdo6-jquery-mobile JQueryMobile
    git clone --recursive https://github.com/gizmore/gdo6-jquery-ui JQueryUI
    # git clone --recursive https://github.com/gizmore/gdo6-language-editor LanguageEditor
    # git clone --recursive https://github.com/gizmore/gdo6-licenses Licenses
    git clone --recursive https://github.com/gizmore/gdo6-links Links
    git clone --recursive https://github.com/gizmore/gdo6-load-on-click LoadOnClick
    git clone --recursive https://github.com/gizmore/gdo6-login Login
    git clone --recursive https://github.com/gizmore/gdo6-login-as LoginAs
    git clone --recursive https://github.com/gizmore/gdo6-logs Logs
    # git clone --recursive https://github.com/gizmore/gdo6-mail-gpg MailGPG
    # git clone --recursive https://github.com/gizmore/gdo6-maintenance Maintenance
    git clone --recursive https://github.com/gizmore/gdo6-maps Maps
    git clone --recursive https://github.com/gizmore/gdo6-markdown Markdown
    # git clone --recursive https://github.com/gizmore/gdo6-material Material
    git clone --recursive https://github.com/gizmore/gdo6-memberlist Memberlist
    ## git clone --recursive https://git@github.com/gizmore/gdo6-mettwitze Mettwitze
    # git clone --recursive https://github.com/gizmore/gdo6-mibbit Mibbit
    git clone --recursive https://github.com/gizmore/gdo6-moment Moment
    ## git clone --recursive https://github.com/gizmore/gdo6-nasdax Nasdax
    git clone --recursive https://github.com/gizmore/gdo6-news News
    git clone --recursive https://github.com/gizmore/gdo6-online-users OnlineUsers
    # git clone --recursive https://github.com/gizmore/gdo6-opentimes OpenTimes
    git clone --recursive https://github.com/gizmore/gdo6-payment Payment
    git clone --recursive https://github.com/gizmore/gdo6-payment-bank PaymentBank
    git clone --recursive https://github.com/gizmore/gdo6-payment-credits PaymentCredits
    git clone --recursive https://github.com/gizmore/gdo6-payment-paypal PaymentPaypal
    ## git clone --recursive https://github.com/gizmore/gdo6-paypal-donation PaypalDonation
    git clone --recursive https://github.com/gizmore/gdo6-pma PhpMyAdmin
    git clone --recursive https://github.com/gizmore/gdo6-pm PM
    git clone --recursive https://github.com/gizmore/gdo6-poll Poll
    git clone --recursive https://github.com/gizmore/gdo6-prism Prism
    git clone --recursive https://github.com/gizmore/gdo6-profile Profile
    # git clone --recursive https://github.com/gizmore/gdo6-push Push
    git clone --recursive https://github.com/gizmore/gdo6-python Python
    git clone --recursive https://github.com/gizmore/gdo6-qrcode QRCode
    ## git clone --recursive https://github.com/gizmore/gdo6-ranzgruppe Ranzgruppe
    git clone --recursive https://github.com/gizmore/gdo6-recovery Recovery
    git clone --recursive https://github.com/gizmore/gdo6-register Register
    ###### EITHER session-db or session-cookie is required
    ###### git clone --recursive https://github.com/gizmore/gdo6-session-db Session
    ###### git clone --recursive https://github.com/gizmore/gdo6-session-cookie Session
    git clone --recursive https://github.com/gizmore/gdo6-sevenzip Sevenzip
    # git clone --recursive https://github.com/gizmore/gdo6-shoutbox Shoutbox
    git clone --recursive https://github.com/gizmore/gdo6-sitemap Sitemap
    ## git clone --recursive https://github.com/gizmore/gdo6-slaytags Slaytags
    ## git clone --recursive https://github.com/gizmore/gdo6-statistics Statistics
    git clone --recursive https://github.com/gizmore/gdo6-tag Tag
    ## git clone --recursive https://github.com/gizmore/gdo6-tbs TBS
    git clone --recursive https://github.com/gizmore/gdo6-tbs-bbmessage TBSBBMessage
    git clone --recursive https://github.com/gizmore/gdo6-tcpdf TCPDF
    git clone --recursive https://github.com/gizmore/gdo6-test-methods TestMethods
    git clone --recursive https://github.com/gizmore/gdo6-tests Tests
    git clone --recursive https://github.com/gizmore/gdo6-theme-switcher ThemeSwitcher
    git clone --recursive https://github.com/gizmore/gdo6-tinymce TinyMCE
    # git clone --recursive https://github.com/gizmore/gdo6-usergroup Usergroup
    git clone --recursive https://github.com/gizmore/gdo6-vote Vote
    git clone --recursive https://github.com/gizmore/gdo6-website Website
    git clone --recursive https://github.com/gizmore/gdo6-websocket Websocket
    ## git clone --recursive https://github.com/gizmore/gdo6-wechall WeChall
    # git clone --recursive https://github.com/gizmore/gdo6-whois Whois
    ## git clone --recursive https://github.com/gizmore/gdo6-wombat Wombat
    git clone --recursive https://github.com/gizmore/gdo6-zip ZIP

### After cloning    

Then make your webserver point to the gdo6 directory and request install/wizard.php in your browser.

### CLI install

    # install gdo via gdoadm.php
    cd gdo6
    php gdoadm.php configure # configure gdo6 installation
    php gdoadm.php install <Module> # install a module
    php gdoadm.php admin <user> <pass> [<email>] # create an admin

    # Try some commands via gdo6/bin/gdo
    gdo admin.clearcache # Call clearcache method of module Admin.
    gdo core.impressum # Call Impressum method of module Core
