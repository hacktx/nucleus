<?hh // strict
/**
 * This file is generated. Do not modify it manually!
 *
 * To re-generate this file run
 * /home/rmlynch/nucleus-beta/vendor/codegyre/robo/robo
 *
 *
 * @generated SignedSource<<541baeed23f17d7d5fa527f137780f7c>>
 */

final class UserMutator {

  private Map<string, mixed> $data = Map {
  };

  private function __construct(private ?int $id = null) {
  }

  public static function create(): this {
    return new UserMutator();
  }

  public static function update(int $id): this {
    return new UserMutator($id);
  }

  public function save(): int {
    $id = $this->id;
    if ($id === null) {
      $this->checkRequiredFields();
      DB::insert("user", $this->data);
      return (int) DB::insertId();
    } else {
      DB::update("user", $this->data, "id=%s", $this->id);
      return $id;
    }
  }

  public function checkRequiredFields(): void {
    $required = Set {
      'id',
      'email',
      'fname',
      'lname',
      'graduation',
      'major',
      'shirt_size',
      'dietary_restrictions',
      'special_needs',
      'birthday',
      'gender',
      'phone_number',
      'school',
      'status',
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

  public function setEmail(string $value): this {
    $this->data["email"] = $value;
    return $this;
  }

  public function setFirstName(string $value): this {
    $this->data["fname"] = $value;
    return $this;
  }

  public function setLastName(string $value): this {
    $this->data["lname"] = $value;
    return $this;
  }

  public function setGraduation(string $value): this {
    $this->data["graduation"] = $value;
    return $this;
  }

  public function setMajor(string $value): this {
    $this->data["major"] = $value;
    return $this;
  }

  public function setShirtSize(string $value): this {
    $this->data["shirt_size"] = $value;
    return $this;
  }

  public function setDietaryRestrictions(string $value): this {
    $this->data["dietary_restrictions"] = $value;
    return $this;
  }

  public function setSpecialNeeds(string $value): this {
    $this->data["special_needs"] = $value;
    return $this;
  }

  public function setBirthday(DateTime $value): this {
    $this->data["birthday"] = $value->format("Y-m-d");
    return $this;
  }

  public function setGender(string $value): this {
    $this->data["gender"] = $value;
    return $this;
  }

  public function setPhoneNumber(string $value): this {
    $this->data["phone_number"] = $value;
    return $this;
  }

  public function setSchool(string $value): this {
    $this->data["school"] = $value;
    return $this;
  }

  public function setStatus(int $value): this {
    $this->data["status"] = $value;
    return $this;
  }
}
