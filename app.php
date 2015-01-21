<?hh

# Get the autoloader going
require_once('lib/AutoLoader.php');
spl_autoload_register('AutoLoader::loadFile');

# Load in external libraries
require 'vendor/autoload.php';

# Setup Mailgun and email
use Mailgun\Mailgun;
Email::$mg = new Mailgun('key');
Email::$domain = 'example.com';
Email::$from = 'hello@example.com';

# Get the user session going
Session::init();

# Prepare the databae
DB::$user = getenv('DB_USER');
DB::$password = getenv('DB_PASS');
DB::$dbName = 'omega';
DB::$port = 3306;

# Call the dispatcher to do its thing
Route::dispatch(
  strtolower(strtok($_SERVER["REQUEST_URI"],'?')),
  strtolower($_SERVER['REQUEST_METHOD'])
);
