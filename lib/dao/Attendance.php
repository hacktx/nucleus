<?hh

class Attendance {

  private int $event_id = 0;
  private int $user_id = 0;
  private string $event_name = '';

  public function __construct(
    int $event_id,
    int $user_id,
    string $event_name
  ): void {
    $this->event_id = $event_id;
    $this->user_id = $user_id;
    $this->event_name = $event_name;
  }

  public static function create(
    int $user_id,
    int $event_id
  ): void {
    DB::insert('attendance', array(
      'user_id' => $user_id,
      'event_id' => $event_id
    ));
  }

  public static function genAllForEvent(int $event_id): array<Attendance> {
    $query = DB::query(
      "SELECT attendance.event_id, attendance.user_id, events.name
      FROM attendance
      INNER JOIN events
      WHERE attendance.event_id = events.id
      AND event_id=%s",
      $event_id
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Attendance(
        (int)$value['event_id'],
        (int)$value['user_id'],
        $value['name']
      );
    }, $query);
  }

  public static function genAllForUser(int $user_id): array<Attendance> {
    $query = DB::query("
      SELECT attendance.event_id, attendance.user_id, events.name
      FROM attendance
      INNER JOIN events
      WHERE attendance.event_id = events.id
      AND user_id=%s",
      $user_id
    );
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Attendance(
        (int)$value['event_id'],
        (int)$value['user_id'],
        $value['name']
      );
    }, $query);
  }

  public function getEventID(): int {
    return $this->event_id;
  }

  public function getUserID(): int {
    return $this->user_id;
  }

  public function getEventName(): string {
    return $this->event_name;
  }
}
