<?hh

enum UserState: int {
  Pending = 0;
  Accepted = 1;
  Waitlisted = 2;
  Rejected = 3;
}

class User {
  private int $id = 0;
  private string $email = '';
  private string $fname = '';
  private string $lname = '';
  private string $access_token = '';
  private string $token = '';
  private UserState $status = UserState::Pending;
  private array<UserRole> $roles = array();

  public static function create(
    League\OAuth2\Client\Provider\MLHUser $mlh_user
  ): ?User {
    DB::query(
      "SELECT * FROM users WHERE id=%d OR email=%s",
      $mlh_user->getId(), $mlh_user->getEmail()
    );

    if(DB::count() != 0) {
      return null;
    }

    DB::insert('users', array(
      'id' => $mlh_user->getId(),
      'email' => $mlh_user->getEmail(),
      'fname' => $mlh_user->getFirstName(),
      'lname' => $mlh_user->getLastName(),
      'status' => UserState::Pending,
    ));

    return self::genByID($mlh_user->getId());
  }

  public function setToken(string $token): void {
    DB::update('users', array(
      'token' => $token
    ), 'id=%s', $this->id);
    $this->token = $token;
  }

  public function getID():int {
    return $this->id;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getFirstName(): string {
    return $this->fname;
  }

  public function getLastName(): string {
    return $this->lname;
  }

  public function getRoles(): array<UserRole> {
    return $this->roles;
  }

  public function getStatus(): UserState {
    return $this->status;
  }

  public function isPending(): bool {
    return $this->status == UserState::Pending;
  }

  public function isAccepted(): bool {
    return $this->status == UserState::Accepted;
  }

  public function isWaitlisted(): bool {
    return $this->status == UserState::Waitlisted;
  }

  public function isRejected(): bool {
    return $this->status == UserState::Rejected;
  }

  public static function genByID($user_id): ?User {
    return self::constructFromQuery('id', $user_id);
  }

  public static function genByEmail($email): ?User {
    return self::constructFromQuery('email', $email);
  }

  public static function genByIDAndToken(int $user_id, string $token): ?User {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE id=%s AND token=%s", $user_id, $token);
    if(!$query) {
      return null;
    }
    $user = self::createFromQuery($query);
    return $user;
  }

  public static function updateStatusByID(UserState $status, int $user_id): void {
    DB::update('users', array('member_status' => $status), "id=%s", $user_id);
  }

  public static function deleteByID($user_id): void {
    DB::delete('users', 'id=%s', $user_id);
  }

  private static function constructFromQuery($field, $query): ?User {
    # Get the user
    $query = DB::queryFirstRow("SELECT * FROM users WHERE " . $field ."=%s", $query);
    if(!$query) {
      return null;
    }
    $user = self::createFromQuery($query);
    $user->roles = Roles::getRoles($user->getID());

    return $user;
  }

  private static function createFromQuery(array $query): User {
    $user = new User();
    $user->id = (int)$query['id'];
    $user->email = $query['email'];
    $user->fname = $query['fname'];
    $user->lname = $query['lname'];
    $user->token = $query['token'];
    $user->status = UserState::assert($query['status']);
    return $user;
  }
}
