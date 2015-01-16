<?php

# Get the autoloader going
require_once('Autoloader.php');
spl_autoload_register('Autoloader::loadFile');

# Get the user session going 
Session::init();
