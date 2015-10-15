![Nucleus](nucleus.png)  
[![Software License](https://img.shields.io/badge/license-EPL-brightgreen.svg)](LICENSE)
[![Code Climate](https://codeclimate.com/github/hacktx/nucleus/badges/gpa.svg)](https://codeclimate.com/github/hacktx/nucleus)
[![Dependency Status](https://gemnasium.com/hacktx/nucleus.svg)](https://gemnasium.com/hacktx/nucleus)

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
Nucleus is backed by MySQL. The schema can be found [here](https://github.com/hacktx/nucleus-vagrant/blob/master/provision/config/schema.sql).
TODO: Add more instructions about MySQL setup.

### Install dependencies
Nucleus uses [Composer](https://getcomposer.org/) to manage dependencies. Once Nucleus is downloaded, run `composer install` in the root of the project.

### Setup the Frontend
Nucleus uses [React](https://facebook.github.io/react/) for the rendering of some views, and [Gulp](http://gulpjs.com) as the front-end build tool. npm is used to manage the frontend dependencies. To start, install node.js and npm, and the run `npm install` in the project root. When that's done, install gulp globally, using `npm install -g gulp`. Then, build the front end by running `gulp build`.

#### Learn more about how Nucleus works on [the wiki](https://github.com/hacktx/nucleus/wiki/How-Nucleus-Works).
