<?hh

class Attendance {

  private int $event_id = 0;
  private int $user_id = 0;

  public function __construct(int $event_id, int $user_id): void {
    $this->event_id = $event_id;
    $this->user_id = $user_id;
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
    $query = DB::query("SELECT * FROM attendance WHERE event_id=%s", $event_id);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Attendance(
        (int)$value['event_id'],
        (int)$value['user_id']
      );
    }, $query);
  }

  public static function genAllForUser(int $user_id): array<Attendance> {
    $query = DB::query("SELECT * FROM attendance WHERE user_id=%s", $user_id);
    if(!$query) {
      return array();
    }
    return array_map(function($value) {
      return new Attendance(
        (int)$value['event_id'],
        (int)$value['user_id']
      );
    }, $query);
  }

  public function getEventID(): int {
    return $this->event_id;
  }

  public function getUserID(): int {
    return $this->user_id;
  }
}
