<?hh

enum UserState: int {
  Applicant = 0;
  Pledge = 1;
  Member = 2;
  Disabled = 3;
}

class User {
  private int $id = 0;
  private string $email = '';
  private string $fname = '';
  private string $lname = '';
  private string $access_token = '';
  private string $token = '';
  private UserState $member_status = UserState::Disabled;
  private array<UserRole> $roles = array();

  public static function create(
    int $id,
    string $email,
    string $fname,
    string $lname
  ): ?User {
    # Make sure a user doesn't already exist with that username or email
    DB::query(
      "SELECT * FROM users WHERE id=%d OR email=%s",
      $id, $email
    );
    if(DB::count() != 0) {
      return null;
    }

    # Insert the user
    DB::insert('users', array(
      'email' => $email,
      'fname' => $fname,
      'lname' => $lname,
      'member_status' => UserState::Applicant
    ));

    return self::genByID($id);
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

  public function getStatusID(): UserState {
    return $this->member_status;
  }

  public function getStatus(): string {
    switch($this->member_status) {
      case UserState::Applicant:
        return 'applicant';
      case UserState::Pledge:
        return 'pledge';
      case UserState::Member:
        return 'member';
      case UserState::Disabled:
        return 'disabled';
    }
  }

  public function isApplicant(): bool {
    return $this->member_status == UserState::Applicant;
  }

  public function isPledge(): bool {
    return $this->member_status == UserState::Pledge;
  }

  public function isMember(): bool {
    return $this->member_status == UserState::Member;
  }

  public function isDisabled(): bool {
    return $this->member_status == UserState::Disabled;
  }

  public function isAdmin(): bool {
    return in_array('admin', $this->roles);
  }

  public function isReviewer(): bool {
    return in_array('reviewer', $this->roles);
  }

  public function isOfficer(): bool {
    return in_array(Roles::Officer, $this->roles);
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
    $user->access_token = $query['token'];
    $user->member_status = UserState::assert($query['member_status']);
    return $user;
  }
}
