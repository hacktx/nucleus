<?hh

final class Announcement {

  protected int $id = 0;
  protected string $text = '';
  protected string $timestamp = '';

  public static function create(
    string $text,
    string $timestamp
  ): ?Announcement {
    DB::insert('announcement', array(
      'text' => $text,
      'timestamp' => date("Y-m-d H:i:s", (int)$timestamp),
    ));

    return self::genByID(DB::insertId());
  }

  public static function genByID(int $id): ?Announcement {
    $query = DB::queryFirstRow('SELECT * FROM announcement WHERE id=%s', $id);
    if (!$query) {
      return null;
    }

    $res = new Announcement();
    $res->id = $query['id'];
    $res->text = $query['text'];
    $res->timestamp = $query['timestamp'];
    return $res;
  }

  public function getText(): string {
    return $this->text;
  }

  public function getTimestamp(): string {
    return $this->timestamp;
  }
}
