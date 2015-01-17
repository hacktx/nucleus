<?hh
class Auth {
  public static function login(
    string $username,
    string $password,
    bool $remember = false
  ): bool {
    $user = User::genByUsername($username);
    if ($user && hash_equals($user->getPassword(), crypt($password, $user->getPassword()))) {
      Session::create($user);
      if ($remember) {
        Cookie::create('id', hash('md5', $user->getUsername()));
      }
      return true;
    }
    return false;
  }

  public static function logout(): void {
    Session::destroy();
  }
}
