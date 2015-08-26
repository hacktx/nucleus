<?hh

class Config {
  public static $configs = null;

  public static function initialize(array $configs): void {
    self::$configs = $configs;
  }

  public static function get(string $key): array {
    invariant(self::$configs !== null, 'Config must be initialized');
    return self::$configs[$key];
  }
}
