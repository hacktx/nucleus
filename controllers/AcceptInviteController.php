<?hh

class AcceptInviteController extends BaseController {
  public static function getPath(): string {
    return '/invite/accept';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserState(array(UserState::Accepted));
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    if ($user->getRoles()->contains(UserRole::Confirmed) ||
        $user->getRoles()->contains(UserRole::Denied)) {
      Flash::set(Flash::ERROR, "You've already responded to your invite");
      Route::redirect(DashboardController::getPath());
    }
    return <nucleus:accept-invite user={Session::getUser()} />;
  }

  public static function post(): void {
    if (isset($_POST['accept']) && isset($_POST['deny'])) {
      http_response_code(400);
      Flash::set(Flash::ERROR, "Something went wrong! Please try again.");
      Route::redirect(self::getPath());
    }

    $user = Session::getUser();

    if (isset($_POST['accept'])) {
      Roles::insert(UserRole::Confirmed, $user->getID());
      Flash::set(Flash::SUCCESS, "You've successfully confirmed!");
    } else {
      Roles::insert(UserRole::Denied, $user->getID());
      Flash::set(
        Flash::SUCCESS,
        "Your invitation was successfully turned down.",
      );
    }

    Route::redirect(DashboardController::getPath());
  }
}
