<?hh

class Event {
  public static function create(
    string $name,
    string $location,
    string $date,
    string $time
  ): Event {
    $unix_timestamp = strtotime($date . ' ' . $time);
    $mysql_timestamp = date('Y-m-d H:i:s',$unix_timestamp);
    DB::insert('events', array(
      'name' => $name,
      'location' => $location,
      'datetime' => $mysql_timestamp 
    ));

    $id = DB::insertId();
    $query = DB::queryFirstRow("SELECT * FROM events WHERE id=%s", $id);
    return self::createFromQuery($query);
  }

  private static function createFromQuery(array $query): Event {
    $event = new Event();
    $event->id = $query['id'];
    $event->name = $query['name'];
    $event->location = $query['location'];
    $event->datetime = $query['datetime'];
    return $event;
  }
}
