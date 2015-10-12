<?hh
class Auth {
  public static function login(int $id): bool {
    $user = User::load($id);

    if (!$user) {
      return false;
    }

    Session::create($user);

    return true;
  }
  
  public static function logout(): void {
    Session::destroy();
  }

  public static function verifyStatus(array<UserStatus> $status): void {
    // No status required
    if (empty($status)) {
      return;
    }

    // No actice session, so no user is logged in.
    if(!Session::isActive()) {
      Flash::set(Flash::ERROR, 'You must be logged in to view this page');
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Route::redirect('/login');
    }

    // Check the users's status against the permitted status
    $user = Session::getUser();
    if(!in_array($user->getStatus(), $status)) {
      Flash::set(Flash::ERROR, 'You do not have permission to view this page');
      Route::redirect('/dashboard');
    }

    return;
  }

  public static function verifyRoles(array<UserRole> $roles): void {
    // No roles required
    if(empty($roles)) {
      return;
    }

    // No actice session, so no user is logged in.
    if(!Session::isActive()) {
      Flash::set(Flash::ERROR, 'You must be logged in to view this page');
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Route::redirect('/login');
    }

    // If the intersection of the user's roles and the required roles is empty,
    // the user does not have any of the required roles to view this page
    $user = Session::getUser();
    $intersection = array_intersect($roles, $user->getRoles());
    if(empty($intersection)) {
      Flash::set(Flash::ERROR, 'You do not have the required roles to access this page');
      Route::redirect('/dashboard');
    }
  }
}
