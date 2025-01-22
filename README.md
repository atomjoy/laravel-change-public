# Laravel from public_html

How to run Laravel from the public_html directory on shared hosting.

## Laravel application directory

Only if you can add Laravel files below public_html in your hosting using FollowSymlinks or SymLinksIfOwnerMatch.

```sh
# Xampp example
composer create-project laravel/laravel D:/www/example.org
```

## Update AppServiceProvider.php

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Create symlinks from public to public_html directory in config/filesystems.php
        config(['filesystems.links' => [
            public_path('storage') => storage_path('app/public'),
            base_path('public_html') => base_path('public')
        ]]);

        // Rewrite public dir to public_html shared hosting
        $this->app->usePublicPath(app()->basePath('public_html'));

        // Or rewrite public dir to public_html shared hosting
        // $this->app->bind('path.public', function () {
        //     return base_path() . '/public_html';
        // });
    }

    public function boot(): void
    {
        // Laravel force https
        // if (App::environment(['staging', 'production'])) {
        //     URL::forceScheme('https');
        // }
    }
}
```

## Create symlinks

If errors occur, remove the public_html directory from the hosting, then copy the files with symbolic links or use the commands via ssh.

```sh
php artisan storage:link
php artisan config:clear
```


## Hosting settings

On small.pl when you add envs dir for the domain in the admin panel.

### Examples .htaccess

```sh
# Run Php 8.2 small.pl hosting
AddType application/x-httpd-php82 .php

# Symlinks
Options -Indexes -MultiViews +SymLinksIfOwnerMatch
# Options -Indexes -MultiViews +FollowSymlinks

# Index file
DirectoryIndex index.php index.html

# Redirect to https
RewriteEngine On
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Non-www
RewriteEngine On
RewriteCond %{HTTP_HOST} ^www\.(.*)$ [NC]
RewriteRule ^(.*)$ https://%1/$1 [R=301,L]

# Non-www
RewriteEngine On
RewriteCond %{HTTP_HOST} ^www.example.org [NC]
RewriteRule ^(.*)$ http://example.org/$1 [L,R=301]
```

## Xampp

### Add local domain

Add host in: C:\Windows\System32\drivers\etc\hosts

```sh
# Local domain
127.0.0.100 example.org www.example.org
```

### Include vhosts directory

C:\xampp\apache\conf\extra\httpd-vhosts

```sh
Include "conf/extra/vhosts/*.conf"
```

### Domain local vhost

C:\xampp\apache\conf\extra\vhosts\example.org.conf

```sh
<VirtualHost 127.0.0.100:80>
    DocumentRoot "D:/www/example.org/public_html"
    DirectoryIndex index.php
    # Doamin here
    ServerName example.org
    ServerAlias www.example.org

    # Create first files for logs
    #ErrorLog "D:/www/example.org/storage/logs/example.org.error.log"
    #CustomLog "D:/www/example.org/storage/logs/example.org.access.log" common

    # Redirect ssl
    #RewriteEngine On
    #RewriteCond %{HTTPS} off
    #RewriteRule (.*) https://%{SERVER_NAME}$1 [R,L]
    
    <Directory "D:/www/example.org/public_html">        
        #Options -Indexes -MultiViews +SymLinksIfOwnerMatch
        Options -Indexes -MultiViews +FollowSymLinks
        AllowOverride all
        Order Deny,Allow
        Allow from all
        Require all granted
    </Directory>

    <Files .env>
        Order allow,deny
        Deny from all
    </Files>

    <FilesMatch "^\.">
        Order allow,deny
        Deny from all
    </FilesMatch>

    <FilesMatch ".(jpg|jpeg|png|gif|ico|webp)$">
        Header set Cache-Control "max-age=86400, public"
    </FilesMatch>
</VirtualHost>

<VirtualHost 127.0.0.100:443>
    DocumentRoot "D:/www/example.org/public_html"
    # Doamin here
    ServerName example.org
    ServerAlias www.example.org

    SSLEngine on
    SSLCertificateFile "conf/ssl.crt/server.crt"
    SSLCertificateKeyFile "conf/ssl.key/server.key"

    <Directory "D:/www/example.org/public_html">
        #Options -Indexes -MultiViews +SymLinksIfOwnerMatch
        Options -Indexes -MultiViews +FollowSymLinks
        AllowOverride all
        Order Deny,Allow
        Allow from all
        Require all granted
    </Directory>

    <Files .env>
        Order allow,deny
        Deny from all
    </Files>

    <FilesMatch "^\.">
        Order allow,deny
        Deny from all
    </FilesMatch>

    <FilesMatch ".(jpg|jpeg|png|gif|ico|webp)$">
        Header set Cache-Control "max-age=86400, public"
    </FilesMatch>
</VirtualHost>
```
