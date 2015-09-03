<?hh

class DashboardController extends BaseController {
  public static function getPath(): string {
    return '/dashboard';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserState(array(
        UserState::Pending,
        UserState::Accepted,
        UserState::Waitlisted,
        UserState::Rejected
      ));
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $status = null;
    $user_status = $user->getStatus();
    switch($user_status) {
      case UserState::Pending:
        $status = "Under Review";
        break;
      case UserState::Accepted:
        $status = "Accepted";
        break;
      case UserState::Waitlisted:
        $status = "Wait Listed";
        break;
      case UserState::Rejected:
        $status = "Rejected";
        break;
    }

    return
      <nucleus:dashboard
        name={$user->getFirstName()}
        status={$status}
        email={$user->getEmail()} />;
  }
}
