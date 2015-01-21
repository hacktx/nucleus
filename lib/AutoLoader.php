<?hh

class AutoLoader {
  public static function loadFile($class): bool {
    if(file_exists('lib/' . $class . '.php')) {
      require_once('lib/' . $class . '.php');
      return true;
    }
    return false;
  }
}
