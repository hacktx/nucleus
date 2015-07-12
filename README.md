# Nucleus
Member identity system and application portal

## Installation
### Setup HHVM
Nucleus runs on Hack, which requires [HHVM](http://hhvm.com/). To install HHVM, follow the instructions [on their website](http://docs.hhvm.com/manual/en/install-intro.install.php).

### Setup Nginx
All Nucleus requests route through app.php in the root of the project, with static resources being pulled from the public folder.
This requires a custom Nginx rule. Here is an example:
```
server {
  listen 80;

  root /var/www;

  server_name nucleus.example.com;
  include hhvm.conf;

  location / {
    try_files /public/$uri /app.php$is_args$args;
  }
}
```
Note the `include hhvm.conf;`. The HHVM install should have created this file in `/etc/nginx` on installation.
If not, [these](http://fideloper.com/hhvm-nginx-laravel) are good instructions on how to configure nginx to use HHVM.

### Setup MySQL
Nucleus is backed by MySQL.

### Install dependencies
Nucleus uses [Composer](https://getcomposer.org/) to manage dependencies. Once Nucleus is downloaded, run `composer install` in the root of the project.

## How Nucleus works
Nucleus follows a Router -> Controller paradigm, where the Views are built right into the entire system

### The Router
All app requests start at `app.php` in the root of the project. This is done via the Nginx rule described above. From there, all services are setup, such as the Session manager, DB interface class, and Email system. This is also where the autoloader is brought into context. So that a manual "require" is not needed each time you use a class, they're simply required on usage by the autoloader in `lib/`. Once all this is done, the Route class is called, which lives in `lib/Route.php`. Inside of this file lives the `$routes` map, containing paths as the keys and Maps like the one below as the values:

```php
'/events/admin' => Map {
  'controller' => 'EventsAdminController',        # required
  'methods' => 'GET|POST',                        # required
  'status' => array(User::Member),                # optional
  'roles' => array(Roles::Admin, Roles::Officer)  # optional
}
```

This maps a request path to a controller and defines which HTTP methods that request is valid on. Additionally, things like the access roles and member status can be defined here, which enforces auth before delegation to the router.

### The Controller
Once the router has matched a path and determined which controller to delegate to, it calls the controller via the technique `$Controller::$method`, where `$Controller` is an instance of the controller class, and `$method` is the HTTP method the request was made one. That means to render a page from a get method, you declare a `get` method in your controller which will get called. Once this function is called, the return value should be some XHP object. This will get passed into the `Render` class which lives in `lib/Render.php`. Render constructs the page content, such as the nav bar at the top, and footer.
