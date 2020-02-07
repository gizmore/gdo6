# Notes on gdo6 with apache2

## Permissions

I run apache2 with mod mpm itk.
This allows switching users easily.


## Document Root

I put my document root in /home/user/www/site.


## Virtual Hosts

This virtual hosts configuration does the following:

- redirect to www
- serve both, http and https
- allow letsencrypt to verify www and non-www domains.

    <VirtualHost *:80>
        ServerName gdo6.com

        DocumentRoot /home/gizmore/www/gdo6

        <Directory "/home/gizmore/www/gdo6">
                Options +Indexes +FollowSymLinks -MultiViews
                AllowOverride All
                Require all granted
        </Directory>
        AssignUserID gizmore gizmore
        ErrorLog /home/gizmore/www/recalcolo.errors.log
        CustomLog /home/gizmore/www/recalcolo.access.log combined

        RewriteCond %{REQUEST_URI} "!/.well-known/acme-challenge/"
        RewriteRule ^/*(.*)$ https://%{HTTP_HOST}/$1 [NE,L,R=301]
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
        ErrorLog /home/gizmore/www/recalcolo.errors.log
        CustomLog /home/gizmore/www/recalcolo.access.log combined
    </VirtualHost>

    <VirtualHost *:443>
        ServerName gdo6.com
        SSLEngine on
        SSLProtocol all -SSLv2
        SSLCipherSuite HIGH:!aNULL:!MD5
        SSLCertificateFile /root/.acme.sh/gdo6.com/fullchain.cer
        SSLCertificateKeyFile  /root/.acme.sh/gdo6.com/gdo6.com.key
        AssignUserID gizmore gizmore
        ErrorLog /home/gizmore/www/recalcolo.errors.log
        CustomLog /home/gizmore/www/recalcolo.access.log combined

        Redirect permanent / https://www.gdo6.com/
    </VirtualHost>

    <VirtualHost *:443>
        ServerName www.gdo6.com
        DocumentRoot /home/gizmore/www/gdo6
        <Directory "/home/gizmore/www/gdo6">
                Options +Indexes +FollowSymLinks -MultiViews
                AllowOverride All
                Require all granted
        </Directory>
        SSLEngine on
        SSLProtocol all -SSLv2
        SSLCipherSuite HIGH:!aNULL:!MD5
        SSLCertificateFile /root/.acme.sh/www.gdo6.com/fullchain.cer
        SSLCertificateKeyFile  /root/.acme.sh/www.gdo6.com/www.gdo6.com.key
        AssignUserID gizmore gizmore
        ErrorLog /home/gizmore/www/recalcolo.errors.log
        CustomLog /home/gizmore/www/recalcolo.access.log combined
    </VirtualHost>


## letsencrypt

I use acme.sh

