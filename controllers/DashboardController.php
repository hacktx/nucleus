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

    $badges = <p />;
    $roles = $user->getRoles();
    foreach($roles as $role) {
      $badges->appendChild(<span class="label label-success">{ucwords($role)}</span>);
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body text-center">
            <div class="col-md-12">
              <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
              <p>{$user->getEmail()}</p>
              {$badges}
            </div>
          </div>
          <div class="panel-body">
            <h3>Application Status: <span class="label label-info">{$status}</span></h3>
          </div>
        </div>
      </x:frag>;
  }
}
