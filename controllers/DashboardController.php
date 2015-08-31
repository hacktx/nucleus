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
      <x:frag>
        <div class="col-md-12 text-center">
          <h3>Thanks for applying, {$user->getFirstName()}! Your application is</h3>
          <h1><span class="label label-info">{$status}</span></h1>
          <p>Can't make it? <a href={DeleteAccountController::getPath()}>Cancel My Application</a></p>
        </div>
      </x:frag>;
  }
}
