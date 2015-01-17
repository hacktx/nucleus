<?hh

# Get the autoloader going
require_once('lib/AutoLoader.php');
spl_autoload_register('AutoLoader::loadFile');

# Get XHP ready
require_once('lib/xhp/init.php');

# Get the user session going
Session::init();

# Call the dispatcher to do its thing
Route::dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
