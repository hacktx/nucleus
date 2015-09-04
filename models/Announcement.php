<?hh

final class Announcement {

  protected string $Text = '';
  protected DateTime $Timestamp = new DateTime();

  public static function genByID(int $id): ?Announcement {
    $query = DB::queryFirstRow('SELECT * FROM announcement WHERE id=%s', $id);
    if (!$query) {
      return null;
    }

    $res = new Announcement();
    $res->Text = $query['text'];
    $res->Timestamp = new DateTime($query['timestamp']);
    return $res;
  }

  public function getText(): string {
    return $this->Text;
  }

  public function getTimestamp(): DateTime {
    return $this->Timestamp;
  }
}
