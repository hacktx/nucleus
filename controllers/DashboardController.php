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
        UserState::Confirmed,
      ),
    );
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $user_status = $user->getStatus();
    $status = UserState::getNames()[$user_status];

    $child = null;
    switch ($user_status) {
      case UserState::Pending:
        $child =
          <x:frag>
            <p class="info">
              Acceptances will roll out in ~7 days. If accepted, you will
              receive a confirmation email at {$user->getEmail()} with further
              instructions.
            </p>
            <div class="footer">
              <p>
                {"Can't make it?"}
                <a href={DeleteAccountController::getPath()}>
                  Cancel My Application
                </a>
              </p>
            </div>
          </x:frag>;
        break;
      case UserState::Accepted:
        $child =
          <p class="info">
            You received an invitation! Respond to it
            <a href={AcceptInviteController::getPath()}>here</a>
          </p>;
        break;
      case UserState::Waitlisted:
        $child =
          <p class="info">
            Keep an eye on your email as we send out more invites! Invites are
            awarded on a first-come-first-serve basis.
          </p>;
        break;
      case UserState::Confirmed:
        $child =
          <p class="info">You successfully accepted your invitation!</p>;
        break;
      case UserState::Rejected:
        $child =
          <p class="info">
            We hope to see you next year! If you are interested in
            volunteering, you can sign up
            <a href="http://goo.gl/forms/8Ygo93YMXS">here</a>
          </p>;
        $status = null;
        break;
    }

    return
      <nucleus:dashboard name={$user->getFirstName()} status={$status}>
        {$child}
      </nucleus:dashboard>;
  }
}
