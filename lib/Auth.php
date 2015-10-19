<?hh
class Auth {
  public static function logout(): void {
    Session::destroy();
  }

  public static function requireLogin(): (function (): bool) {
    return () ==> {
      if (!Session::isActive()) {
        Flash::set('redirect', $_SERVER['REQUEST_URI']);
        Flash::set(Flash::ERROR, 'You must be logged in to view this page');
        Route::redirect(FrontpageController::getPath());
      }

      return true;
    };
  }

  public static function verifyStatus(Vector<UserStatus> $status): void {
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

  public static function verifyRoles(Vector<UserRole> $roles): void {
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

  public static function runChecks(Vector<(function (): bool)> $checks): void {
    foreach($checks as $check) {
      if(!$check()) {
        Flash::set(Flash::ERROR, 'You do not have permission to view this page');
        Route::redirect('/dashboard');
      }
    }
  }
}
