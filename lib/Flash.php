<?hh

class Flash {
  const ERROR = 'error';
  const SUCCESS = 'success';

  public static function set($key, $value): void {
    if(!isset($_SESSION['flash'])) {
      $_SESSION['flash'] = Map {};
    }
    $_SESSION['flash'][$key] = $value;
  }

  public static function get($key): mixed {
    if(isset($_SESSION['flash']) && isset($_SESSION['flash'][$key])) {
      $value = $_SESSION['flash'][$key];
      unset($_SESSION['flash'][$key]);
      return $value;
    }
  }

  public static function exists($key): bool {
    return isset($_SESSION['flash']) && isset($_SESSION['flash'][$key]);
  }
}
