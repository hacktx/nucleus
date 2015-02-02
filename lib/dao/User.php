<?hh

class User {

  const Applicant = 0;
  const Pledge = 1;
  const Member = 2;

  private int $id = 0;
  private string $username = '';
  private string $password = '';
  private string $email = '';
  private string $fname = '';
  private string $lname = '';
  private int $member_status = 0;
  private array $roles = array();

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

    return self::genByUsername($username);
  }

  public function getID():int {
    return $this->id;
  }

  public function getUsername(): string {
    return $this->username;
  }

  public function getPassword(): string {
    return $this->password;
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

  public function getRoles(): array {
    return $this->roles;
  }

  public function getStatusID(): int {
    return $this->member_status;
  }

  public function getStatus(): string {
    switch($this->member_status) {
      case self::Applicant:
        return 'applicant';
      case self::Pledge:
        return 'pledge';
      case self::Member:
        return 'member';
      default:
        return 'unknown';
    }
  }

  public function isApplicant(): bool {
    return $this->member_status == self::Applicant;
  }

  public function isPledge(): bool {
    return $this->member_status == self::Pledge;
  }

  public function isMember(): bool {
    return $this->member_status == self::Member;
  }

  public function isAdmin(): bool {
    return in_array('admin', $this->roles);
  }

  public function isReviewer(): bool {
    return in_array('reviewer', $this->roles);
  }

  public function isEventAdmin(): bool {
    return in_array(Roles::EventAdmin, $this->roles);
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
    $user->username = $query['username'];
    $user->password = $query['password'];
    $user->email = $query['email'];
    $user->fname = $query['fname'];
    $user->lname = $query['lname'];
    $user->member_status = (int)$query['member_status'];
    return $user;
  }
}
