<?hh
class Auth {
  public static function login(
    string $username,
    string $password
  ): bool {
    $user = User::genByUsername($username);
    if ($user && hash_equals($user->getPassword(), crypt($password, $user->getPassword()))) {
      Session::create($user);
      return true;
    }
    return false;
  }

  public static function logout(): void {
    Session::destroy();
  }
}
