<?hh
class Auth {
  public static function logout(): void {
    Session::destroy();
  }

  public static function requireLogin(): (function(): bool) {
    return () ==> {
      if (!Session::isActive()) {
        Flash::set('redirect', $_SERVER['REQUEST_URI']);
        Flash::set(Flash::ERROR, 'You must be logged in to view this page');
        Route::redirect(FrontpageController::getPath());
      }

      return true;
    };
  }

  public static function requireState(
    Vector<UserState> $status,
  ): (function(): bool) {
    if (!Session::isActive()) {
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Flash::set(Flash::ERROR, 'You must be logged in to view this page');
      Route::redirect(FrontpageController::getPath());
    }

    return () ==> {
      // Check the users's status against the permitted status
      $user = Session::getUser();
      return in_array($user->getStatus(), $status);
    };
  }

  public static function requireRoles(
    Vector<UserRole> $roles,
  ): (function(): bool) {
    if (!Session::isActive()) {
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Flash::set(Flash::ERROR, 'You must be logged in to view this page');
      Route::redirect(FrontpageController::getPath());
    }

    return () ==> {
      // If the intersection of the user's roles and the required roles is empty,
      // the user does not have any of the required roles to view this page
      $user = Session::getUser();
      $intersection = array_intersect($roles, $user->getRoles());
      return !empty($intersection);
    };
  }

  public static function runChecks(Vector<(function(): bool)> $checks): void {
    foreach ($checks as $check) {
      if (!$check()) {
        Flash::set(
          Flash::ERROR,
          'You do not have permission to view this page',
        );
        Route::redirect(DashboardController::getPath());
      }
    }
  }
}
