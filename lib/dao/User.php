<?hh

class User {

  public const Applicant = 0;
  public const Pledge = 1;
  public const Member = 2;

  public static function create(
    $username,
    $password,
    $email,
    $fname,
    $lname
  ): ?User {
    # Make sure a users doesn't already exist with that username
    DB::query("SELECT * FROM users WHERE username=%s", $username);
    if(DB::count() != 0) {
      return null;
    }

    # Create the password hash
    $salt = strtr(base64_encode(mcrypt_create_iv(16, MCRYPT_DEV_URANDOM)), '+', '.');
    $salt = sprintf("$2a$%02d$", 10) . $salt;
    $hash = crypt($password, $salt);

    # Insert the user
    DB::insert('users', array(
      'username' => $username,
      'password' => $hash,
      'email' => $email,
      'fname' => $fname,
      'lname' => $lname,
      'member_status' => 0
    ));
    $query = DB::queryFirstRow("SELECT * FROM users WHERE username=%s", $username);
    return self::createFromQuery($query);
  }

  public function getID() {
    return $this->id;
  }

  public function getUsername() {
    return $this->username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function getEmail() {
    return $this->email;
  }

  public function getFirstName() {
    return $this->fname;
  }

  public function getLastName() {
    return $this->lname;
  }

  public function isApplicant(): bool {
    return $this->member_status == 0;
  }

  public function isPledge(): bool {
    return $this->member_status == 1;
  }

  public function isMember():bool {
    return $this->member_status == 2;
  }

  public function isAdmin(): bool {
    return (bool)$this->admin;
  }

  public function isReviewer(): bool {
    return (bool)$this->reviewer;
  }

  public static function genByID($user_id): ?User {
    return self::constructFromQuery('id', $user_id);
  }

  public static function genByUsername($username): ?User {
    return self::constructFromQuery('username', $username);
  }

  public static function genByEmail($email): ?User {
    return self::constructFromQuery('email', $email);
  }

  public static function updateStatusByID(int $status, int $user_id): void {
    DB::update('users', array('member_status' => $status), "id=%s", $user_id);
  }

  public static function setRoleByID(string $role, bool $value, int $user_id): void {
    BD::update('users', array($role => $value), "id=%s", $user_id);
  }

  public static function deleteByID($user_id): void {
    DB::delete('users', 'id=%s', $user_id);
  }

  private static function constructFromQuery($field, $query): ?User {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE " . $field ."=%s", $query);
    if(!$query) {
      return null;
    }
    return self::createFromQuery($query);
  }

  private static function createFromQuery(array $query): User {
    $user = new User();
    $user->id = $query['id'];
    $user->username = $query['username'];
    $user->password = $query['password'];
    $user->email = $query['email'];
    $user->fname = $query['fname'];
    $user->lname = $query['lname'];
    $user->member_status = $query['member_status'];
    $user->admin = $query['admin'];
    $user->reviewer = $query['reviewer'];
    return $user;
  }
}
