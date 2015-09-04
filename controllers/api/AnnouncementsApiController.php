<?hh

class AnnouncementsApiController extends BaseController {
  public static function getPath(): string {
    return '/api/announcements';
  }

  public static function get(): array<Map<string, string>> {
    $query = DB::query("SELECT * FROM announcement ORDER BY timestamp");

    $response = array();
    foreach($query as $row) {
      $response[] = Map {
        'text' => $row['text'],
        'ts' => $row['timestamp']
      };
    }

    return $response;
  }
}
