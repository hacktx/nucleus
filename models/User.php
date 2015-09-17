<?hh

enum UserState : int {
  Pending = 0;
  Accepted = 1;
  Waitlisted = 2;
  Rejected = 3;
  Confirmed = 4;
}

class User {
  private Map<string, mixed> $data;
  private Set<UserRole> $roles = Set {};

  private function __construct(Map<string, mixed> $data) {
    $this->data = $data;
  }

  public static function create(
    League\OAuth2\Client\Provider\MLHUser $mlh_user,
  ): ?User {
    DB::query(
      "SELECT * FROM users WHERE id=%d OR email=%s",
      $mlh_user->getId(),
      $mlh_user->getEmail(),
    );

    if (DB::count() != 0) {
      return null;
    }

    DB::insert(
      'users',
      array(
        'id' => $mlh_user->getId(),
        'email' => $mlh_user->getEmail(),
        'fname' => $mlh_user->getFirstName(),
        'lname' => $mlh_user->getLastName(),
        'graduation' => $mlh_user->getGraduation(),
        'major' => $mlh_user->getMajor(),
        'shirt_size' => $mlh_user->getShirtSize(),
        'dietary_restrictions' => $mlh_user->getDietaryRestrictions(),
        'special_needs' => $mlh_user->getSpecialNeeds(),
        'birthday' => $mlh_user->getBirthday(),
        'gender' => $mlh_user->getGender(),
        'phone_number' => $mlh_user->getPhoneNumber(),
        'school' => $mlh_user->getSchool(),
        'status' => UserState::Pending,
      ),
    );

    return self::genByID($mlh_user->getId());
  }

  public function getID(): int {
    return (int) $this->data['id'];
  }

  public function getEmail(): string {
    return (string) $this->data['email'];
  }

  public function getFirstName(): string {
    return (string) $this->data['fname'];
  }

  public function getLastName(): string {
    return (string) $this->data['lname'];
  }

  public function getRoles(): Set<UserRole> {
    return $this->roles;
  }

  public function getStatus(): UserState {
    return UserState::assert($this->data['status']);
  }

  public function getCreated(): DateTime {
    return new DateTime($this->data['created']);
  }

  public function getGender(): string {
    return (string) $this->data['gender'];
  }

  public function getSchool(): string {
    return (string) $this->data['school'];
  }

  public function getMajor(): string {
    return (string) $this->data['major'];
  }

  public function getAge(): int {
    return
      (new DateTime($this->data['birthday']))->diff(new DateTime('today'))->y;
  }

  public function isPending(): bool {
    return $this->data['status'] == UserState::Pending;
  }

  public function isAccepted(): bool {
    return $this->data['status'] == UserState::Accepted;
  }

  public function isWaitlisted(): bool {
    return $this->data['status'] == UserState::Waitlisted;
  }

  public function isRejected(): bool {
    return $this->data['status'] == UserState::Rejected;
  }

  public function delete(): void {
    self::deleteByID($this->data['id']);
  }

  public static function genByID($user_id): ?User {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE id=%s", $user_id);
    if (!$query) {
      return null;
    }
    $user = new User(new Map($query));
    $user->roles = Roles::getRoles($user->getID());

    return $user;
  }

  public static function genByEmail(string $email): ?User {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $email);
    if (!$query) {
      return null;
    }
    $user = new User(new Map($query));
    $user->roles = Roles::getRoles($user->getID());

    return $user;
  }

  public static function updateStatusByID(
    UserState $status,
    int $user_id,
  ): void {
    DB::update('users', array('status' => $status), "id=%s", $user_id);
  }

  public static function deleteByID($user_id): void {
    DB::delete('users', 'id=%s', $user_id);
  }
}
