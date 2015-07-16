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

// Prepare the databae
DB::$user = $configs['DB']['user'];
DB::$password = $configs['DB']['password'];
DB::$dbName = $configs['DB']['name'];
DB::$port = $configs['DB']['port'];

// Setup Mailgun and email
use Mailgun\Mailgun;
Email::$mg = new Mailgun($configs['Mailgun']['key']);
Email::$domain = $configs['Mailgun']['domain'];
Email::$from = $configs['Mailgun']['from'];

// Setup Parse
ParseClient::initialize(
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
