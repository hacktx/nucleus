![Nucleus](nucleus.png)  
[![Software License](https://img.shields.io/badge/license-EPL-brightgreen.svg)](LICENSE)
[![Code Climate](https://codeclimate.com/github/hacktx/nucleus/badges/gpa.svg)](https://codeclimate.com/github/hacktx/nucleus)
[![Dependency Status](https://gemnasium.com/hacktx/nucleus.svg)](https://gemnasium.com/hacktx/nucleus)

Hackathon attendee identity system and application portal

## Installation
### Setup Environment
You can find instructions to setup the Nucleus environment on the [wiki](https://github.com/hacktx/nucleus/wiki/Environment-Setup).

### Setup configurations
In the root of the project folder, there it the file `exampleconfig.ini`. Copy this to `config.ini` and fill out the desired fields. The DB fields are necessary for the service to run.

### Install dependencies
In the root of the project folder, run `composer install` to install the PHP dependencies, and `npm install` for the frontend dependencies.

### Build the Frontend
In the root of the project folder, run `gulp build` to compile the frontend.

#### Learn more about how Nucleus works on [the wiki](https://github.com/hacktx/nucleus/wiki/How-Nucleus-Works).
