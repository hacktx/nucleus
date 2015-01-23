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

  public static function verifyStatus(?array $status): void {
    # Null status array requires no minimum member status
    if(!$status) {
      return;
    }

    if(!Session::isActive()) {
      Flash::set('error', 'You must be logged in to view this page');
      Route::redirect('/login');
    }

    $user = Session::getUser();
    if(!in_array($user->getStatus(), $status)) {
      Flash::set('error', 'You do not have permission to view this page');
      Route::redirect('/dashboard');
    }

    return;
  }

  public static function verifyRoles(?array $roles): void {
    # Null roles array requires no specific roles
    if(!$roles) {
      return;
    }

    if(!Session::isActive()) {
      Flash::set('error', 'You must be logged in to view this page');
      Route::redirect('/login');
    }

    $user = Session::getUser();
    $intersection = array_intersect($roles, $user->getRoles());

    if(empty($intersection)) {
      Flash::set('error', 'You do not have the required roles to access this page');
      Route::redirect('/dashboard');
    }
  }
}
