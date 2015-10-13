<?hh // strict
/**
 * This file is partially generated. Only make modifications between BEGIN
 * MANUAL SECTION and END MANUAL SECTION designators.
 *
 * @partially-generated SignedSource<<3067df1e39651a1661b3f73dcc17f712>>
 */

final class AnnouncementMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new AnnouncementMutator();
  }

  public static function update(int $id): this {
    return new AnnouncementMutator($id);
  }

  public static function delete(int $id): void {
    DB::delete("announcement", "id=%s", $id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("announcement", $this->data);
      return (int) DB::insertId();
    } else {
      DB::update("announcement", $this->data, "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'id',
      'text',
      'timestamp',
    };
    $missing = $required->removeAll($this->data->keys());;
    invariant(
      $missing->isEmpty(),
      "The following required fields are missing: ".implode(", ", $missing),
    );
  }

  public function setID(int $value): this {
    $this->data["id"] = $value;
    return $this;
  }

  public function setText(string $value): this {
    $this->data["text"] = $value;
    return $this;
  }

  public function setTimestamp(DateTime $value): this {
    $this->data["timestamp"] = $value->format("Y-m-d");
    return $this;
  }

  /* BEGIN MANUAL SECTION AnnouncementMutator_footer */
  // Insert additional methods here
  /* END MANUAL SECTION */
}
