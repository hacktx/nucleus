<?hh

class User {

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
      'member' => false,
      'admin' => false
    ));
    $query = DB::queryFirstRow("SELECT * FROM users WHERE username=%s", $username);
    return self::createFromQuery($query);
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

  public function isMember() {
    return $this->isMember;
  }

  public function isAdmin() {
    return $this->isAdmin;
  }

  public static function genByUsername($username): ?User {
    return self::constructFromQuery('username', $username);
  }

  public static function genByEmail($email): ?User {
    return self::constructFromQuery('email', $email);
  }

  private static function constructFromQuery($field, $query): ?User {
    $query = DB::queryFirstRow("SELECT * FROM users WHERE " . $field ."=%s", $query);
    return self::createFromQuery($query);
  }

  private static function createFromQuery(array $query): User {
    $user = new User();
    $user->username = $query['username'];
    $user->password = $query['password'];
    $user->email = $query['email'];
    $user->fname = $query['fname'];
    $user->lname = $query['lname'];
    $user->isMember = $query['member'];
    $user->isAdmin = $query['admin'];
    return $user;
  }
}
