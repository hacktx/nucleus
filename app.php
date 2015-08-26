<?hh

// Load in external libraries
require 'vendor/autoload.php';

// Set the app's timezone to central
date_default_timezone_set('America/Chicago');

if(!file_exists('config.ini')) {
  error_log("Config file does not exist");
  die;
}

$configs = parse_ini_file('config.ini', true);

Config::initialize(new Map($configs));

// Prepare the databae
DB::$user = $configs['DB']['user'];
DB::$password = $configs['DB']['password'];
DB::$dbName = $configs['DB']['name'];
DB::$port = $configs['DB']['port'];

// Setup email
Email::initialize(new SendGridEmailClient(
  $configs['SendGrid']['api_key'],
  $configs['SendGrid']['from_email']
));

// Setup Parse
Parse\ParseClient::initialize(
  $configs['Parse']['app_id'],
  $configs['Parse']['rest_key'],
  $configs['Parse']['master_key']
);

// Get the user session going
Session::init();

// Call the dispatcher to do its thing
Route::dispatch(
  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
  $_SERVER['REQUEST_METHOD']
);
