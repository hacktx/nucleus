<?hh

class User {

  const Applicant = 'applicant';
  const Pledge = 'pledge';
  const Member = 'member';

  private int $id;
  private string $username;
  private string $password;
  private string $email;
  private string $fname;
  private string $lname;
  private string $member_status;
  private array $roles;

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

  public function getEmail() {
    return $this->email;
  }

  public function getFirstName() {
    return $this->fname;
  }

  public function getLastName() {
    return $this->lname;
  }

  public function getRoles(): array {
    return $this->roles;
  }

  public function getStatus(): string {
    return $this->member_status;
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

  public static function addRoleByID(string $role, int $user_id): void {
    DB::insert('roles', array(
      'user_id' => $user_id,
      'role' => $role
    ));
  }

  public static function removeRoleByID(string $role, int $user_id): void {
    DB::delete('roles', 'user_id=%s AND role=%s', $user_id, $role);
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

    # Get the roles
    $query = DB::query("SELECT role FROM roles WHERE user_id=%s", $user->getID());
    $roles = array_map(function($value) {
      return $value['role'];
    }, $query);
    $user->roles = $roles;
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
    $user->member_status = self::getStatusByStatusID((int)$query['member_status']);
    return $user;
  }

  private static function getStatusByStatusID(int $status_id): string {
    switch($status_id) {
      case 0:
        return self::Applicant;
      case 1:
        return self::Pledge;
      case 2:
        return self::Member;
      default:
        return 'unknown';
    }
  }
}
