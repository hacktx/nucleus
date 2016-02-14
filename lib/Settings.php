<?hh

class Settings {
  public static function get(string $key): bool {
    $query = DB::queryFirstRow("SELECT * FROM settings WHERE name=%s", $key);
    if (!$query) {
      return false;
    }
    return filter_var($query['value'], FILTER_VALIDATE_BOOLEAN);
  }

  public static function set(string $key, mixed $value): void {
    $data = Map {
      'name' => $key,
      'value' => $value
    };
    DB::insertUpdate('settings', $data->toArray());
  }
}
