# A RAW - PHP REST API

This was build to to support **Shared Hosting** on Hostinger.com. I tried using composer with packages like: `leafphp` or `slim php` on their shared hosting via SSH. While it did install it, sadly the routes has more issues than necessary. I get the feeling that litespeed server is not an appropriate replacement for apache. Therefore I saw the need to build a `raw php rest api` for my needs. - This could technically be adapted for a different structure.

## File structure

```
└── api/
    ├── htaccess
    ├── .env
    ├── Database.php
    ├── index.php
    └── src/
        ├── Controllers/
        │   └── RoleControllers.php
        └── Routes/
            └── RoleRoutes.php
```

## Database

This is currently uses a `MariaDB` part of the Shared Hosting service. You will need to configure the database yourself. And then provide a `.env` - You'll notice there is commented out `Production` section. This is for your live environment. To build this, I utilized Docker which is why the: **DB_HOST** is set to `mariadb`. However, for the production, I will comment out the Development and uncomment the Production along with supplying the correct address for the database. And of course providing the values for the other values.

```yaml
# .env
# # Production
# DB_HOST=XXX.XXX.XXX.XXX

# Development
DB_HOST=mariadb

# Common
DB_NAME=
DB_USER=
DB_PASS=
```

## .htaccess

To secure the .env file I had to create a .htaccess file. Here is the configuration for it.

```ruby
RewriteEngine On

# Block access to .env file
<Files ".env">
    Require all denied
</Files>

# Send all requests to index.php
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```
