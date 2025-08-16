# A RAW PHP REST API

This was build to to support **Shared Hosting** on Hostinger.com. I tried using composer with packages like: `leafphp` or `slim php` on their shared hosting via SSH. While it did install it, sadly the routes has more issues than necessary. I get the feeling that litespeed server is not an appropriate replacement for apache. Therefore I saw the need to build a `raw php rest api` for my needs. - This could technically be adapted for a different structure.

### File structure

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
