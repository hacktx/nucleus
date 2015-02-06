# Omega
Member identity system and application portal

## Installation
### Setup HHVM
Omega runs on Hack, which requires [HHVM](http://hhvm.com/). To install HHVM, follow the instructions [on their website](http://docs.hhvm.com/manual/en/install-intro.install.php).

### Setup Nginx
All Omega requests route through app.php in the root of the project, with static resources being pulled from the public folder.
This requires a custom Nginx rule. Here is an example:
```
server {
  listen 80;

  root /var/www;

  server_name omega.example.com;
  include hhvm.conf;

  location / {
    try_files /public/$uri /app.php$is_args$args;
  }
}
```
Note the `include hhvm.conf;`. The HHVM install should have created this file in `/etc/nginx` on installation.
If not, [these](http://fideloper.com/hhvm-nginx-laravel) are good instructions on how to configure nginx to use HHVM.

### Setup MySQL
Omega is backed by MySQL and the schema can be found at [TexasLAN/Omega-schema](https://github.com/TexasLAN/Omega-schema).
Once MySQL is installed, create a database 'omega' and a user with access to that database. Either edit app.php to use that
user or set them as environment variables (`DB_USER`, `DB_PASS`).

### Install dependencies
Omega uses [Composer](https://getcomposer.org/) to manage dependencies. Once Omega is downloaded, run `composer install` in the root of the project.
