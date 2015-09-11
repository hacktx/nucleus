<?hh // strict

class DashboardController extends BaseController {
  public static function getPath(): string {
    return '/dashboard';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserState(
      array(
        UserState::Pending,
        UserState::Accepted,
        UserState::Waitlisted,
        UserState::Rejected,
      ),
    );
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $status = null;
    $user_status = $user->getStatus();

    $child = null;
    if ($user_status == UserState::Pending) {
      $child =
        <x:frag>
          <p class="info">
            Acceptances will roll out in ~7 days. If accepted, you will receive
            a confirmation email at {$user->getEmail()} with further
            instructions.
          </p>
          <div class="footer">
            <p>
              Can't make it?
              <a href={DeleteAccountController::getPath()}>
                Cancel My Application
              </a>
            </p>
          </div>
        </x:frag>;
    } else if ($user_status == UserState::Accepted) {
      if ($user->getRoles()->contains(UserRole::Confirmed)) {
        $child =
          <p class="info">You successfully accepted your invitation!</p>;
      } else if ($user->getRoles()->contains(UserRole::Denied)) {
        $child =
          <p class="info">You successfully turned down your invitation</p>;
      } else {
        $child =
          <p class="info">
            You received an invitation! Respond to it
            <a href={AcceptInviteController::getPath()}>here</a>
          </p>;
      }
    }

    return
      <nucleus:dashboard
        name={$user->getFirstName()}
        status={UserState::getNames()[$user_status]}>
        {$child}
      </nucleus:dashboard>;
  }
}
