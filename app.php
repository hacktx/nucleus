<?hh

# Load in external libraries
require 'vendor/autoload.php';

# Setup Mailgun and email
use Mailgun\Mailgun;
Email::$mg = new Mailgun('key');
Email::$domain = 'example.com';
Email::$from = 'hello@example.com';

# Prepare the databae
DB::$user = getenv('DB_USER');
DB::$password = getenv('DB_PASS');
DB::$dbName = 'omega';
DB::$port = 3306;

# Get the user session going
Session::init();

# Call the dispatcher to do its thing
Route::dispatch(
  parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH),
  $_SERVER['REQUEST_METHOD']
);
