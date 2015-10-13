<?hh
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<7d8f29e8e8044e578602e6e5c8155a07>>
 */

final class Announcement {

  private function __construct(private Map<string, mixed> $data) {
  }

  public static function load(int $id): ?Announcement {
    $result = DB::queryFirstRow("SELECT * FROM announcement WHERE id=%s", $id);
    if (!$result) {
      return null;
    }
    return new Announcement(new Map($result));
  }

  public function getID(): int {
    return (int) $this->data['id'];
  }

  public function getText(): string {
    return (string) $this->data['text'];
  }

  public function getTimestamp(): DateTime {
    return new DateTime($this->data['timestamp']);
  }

  /* BEGIN MANUAL SECTION Announcement_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
