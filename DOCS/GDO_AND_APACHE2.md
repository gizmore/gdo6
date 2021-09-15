# Notes on gdo6 with apache2

## Permissions

I run apache2 with mod mpm itk.

This allows switching users easily.


## Document Root

I put my document root in /home/user/www/gdo6.


## Virtual Hosts

A starting point might be:

```
    <VirtualHost *:80>
        ServerName gdo6.com
        DocumentRoot /home/gizmore/www/gdo6
        <Directory "/home/gizmore/www/gdo6">
                Options +Indexes +FollowSymLinks -MultiViews
                AllowOverride All
                Require all granted
        </Directory>
        AssignUserID gizmore gizmore
        ErrorLog /home/gizmore/gdo6.errors.log
        CustomLog /home/gizmore/gdo6.access.log combined
        #RewriteCond %{REQUEST_URI} "!/.well-known/acme-challenge/" not needed anymore?
        #RewriteRule ^/*(.*)$ https://%{HTTP_HOST}/$1 [NE,L,R=301]
    </VirtualHost>
    <VirtualHost *:80>
        ServerName www.gdo6.com
        DocumentRoot /home/gizmore/www/gdo6
        <Directory "/home/gizmore/www/gdo6">
                Options +Indexes +FollowSymLinks -MultiViews
                AllowOverride All
                Require all granted
        </Directory>
        AssignUserID gizmore gizmore
        ErrorLog /home/gizmore/gdo6.errors.log
        CustomLog /home/gizmore/gdo6.access.log combined
    </VirtualHost>
```

## letsencrypt

I use acme.sh
Try `acme.sh --issue --domain gdo6.com --web-root /home/gdo6/www/gdo6 --apache`
