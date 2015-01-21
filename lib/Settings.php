<?hh

class Settings {
  public static function get(string $key): mixed {
    $query = DB::queryFirstRow("SELECT * FROM settings WHERE key=%s", $key);
    return $query['value'];
  }

  public static function set(string $key, mixed $value): void {
    DB::insertUpdate('settings', array(
      'key' => $key,
      'value' => $value
    ));
  }
}
