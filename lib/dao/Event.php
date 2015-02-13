<?hh

class Event {

  private int $id = 0;
  private string $name = '';
  private string $location = '';
  private string $datetime = '';

  public function __construct(
    int $id,
    string $name,
    string $location,
    string $datetime
  ): void {
    $this->id = $id;
    $this->name = $name;
    $this->location = $location;
    $this->datetime = $datetime;
  }

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

  public static function genAllFuture(): array<Event> {
    $query = DB::query("
      SELECT * FROM events
      WHERE datetime >= CURDATE()
      ORDER BY datetime"
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Event(
        (int)$value['id'],
        $value['name'],
        $value['location'],
        $value['datetime']
      );
    }, $query);
  }

  public static function genAllPast(): array<Event> {
    $query = DB::query("
      SELECT * FROM events
      WHERE datetime < CURDATE()
      ORDER BY datetime"
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Event(
        (int)$value['id'],
        $value['name'],
        $value['location'],
        $value['datetime']
      );
    }, $query);
  }

  public static function genByID(int $id): ?Event {
    $query = DB::queryFirstRow("SELECT * FROM events WHERE id=%s", $id);
    if(!$query) {
      return null;
    }
    return new Event(
      (int)$query['id'],
      $query['name'],
      $query['location'],
      $query['datetime']
    );
  }

  public static function deleteByID(int $id): void {
    DB::delete('events', 'id=%s', $id);
  }

  public function getID(): int {
    return $this->id;
  }

  public function getName(): string {
    return $this->name;
  }

  public function getLocation(): string {
    return $this->location;
  }

  public function getDatetime(): string {
    $timestamp = strtotime($this->datetime);
    return date('n/j/Y \@ g:i A', $timestamp);
  }
}
