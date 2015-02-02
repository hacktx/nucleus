<?hh

class Attendance {
  public static function create(
    int $user_id,
    int $event_id
  ): void {
    DB::insert('attendance', array(
      'user_id' => $user_id,
      'event_id' => $event_id
    ));
  }

  public static function getAllForEvent(int $event_id): array {
    $query = DB::query("SELECT * FROM attendance WHERE event_id=%s", $event_id);
    return $query ? $query : array();
  }

  public static function getAllForUser(int $user_id): array {
    $query = DB::query("SELECT * FROM attendance WHERE user_id=%s", $user_id);
    return $query ? $query : array();
  }
}
