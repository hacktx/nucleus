<?hh

class Event {
  public static function create(
    string $name,
    string $location,
    string $date,
    string $time
  ): void {
    $unix_timestamp = strtotime($date . ' ' . $time);
    $mysql_timestamp = date('Y-m-d H:i:s',$unix_timestamp);
    DB::insert('events', array(
      'name' => $name,
      'location' => $location,
      'datetime' => $mysql_timestamp
    ));
  }

  public static function getAll(): array {
    $query = DB::query("SELECT * FROM events WHERE datetime >= CURDATE()");
    return $query ? $query : array();
  }

  public static function deleteByID(int $id) {
    DB::delete('events', 'id=%s', $id);
  }
}
