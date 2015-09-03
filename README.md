# Nucleus
[![Software License](https://img.shields.io/badge/license-EPL-brightgreen.svg?style=flat-square)](LICENSE)
[![Code Climate](https://codeclimate.com/github/hacktx/nucleus/badges/gpa.svg)](https://codeclimate.com/github/hacktx/nucleus)

Hackathon attendee identity system and application portal

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
TODO: Add more instructions about MySQL setup.

### Install dependencies
Nucleus uses [Composer](https://getcomposer.org/) to manage dependencies. Once Nucleus is downloaded, run `composer install` in the root of the project.

### Build the URI Map and Autoloader
Nucleus uses [Robo](robo.li) as a task runner and build tool. This should have been installed with the rest of the dependencies from Composer. To generate the URI map and the autoload map, which are required for Nucleus to run, run `vendor/bin/robo build` (or just `robo build` if you have it installed globally). Creating or deleting a file, or updating a controller's path will require a re-build.

## How Nucleus works
Nucleus follows a rough MVC paradigm, with a top level router, models to contol database access, views in the form of XHP, and controllers which handle the bulk of the logic.

### The Router
All app requests start at `app.php` in the root of the project. This is done via the Nginx rule described above. From there, all services are setup, such as the Session manager, DB interface class, and Email system. This is also where the autoloader is brought into context. So that a manual "require" is not needed each time you use a class, they're simply required on usage by the autoloader from composer. Once all this is done, the Route class is called, which lives in `lib/Route.php`. This class includes the URIMap which was generated with robo, which maps a request path to a controller. Once a controller is selected, it is instanciated, the configurations are retreived and verified, and the controller is delegated.

### The Controller
Controllers are the powerhouses behind Nucleus; they carry the bulk of the logic and create the views which render into either HTML or JSON. All controllers extend `BaseController`, which requires them to define a method `getPath`. This method will return the path the controller will respond to requests on, and is used by the build step in robo to generte the URI map. Additionally, a controller can define a `getConfig` option, which allows the controller to specify things like the required user state or roles to view that controller.

A controller has the option to return either an `:xhp` object, which will render into HTML in the main view, or a `Map`, which will return a JSON object. Typically, only API endpoints should return JSON.

### The Model
A model is a simple object which defines ways to retreive and populate an object from the database, and mutations to update the data in the database. They are the main way to interact with the database, and most SQL queries should be contained within them.
