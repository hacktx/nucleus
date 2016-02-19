<?hh

class CheckinController extends BaseController {
  public static function getPath(): string {
    return '/dashboard/checkin';
  }

  public static function getConfig(): ControllerConfig {
    return
      (new ControllerConfig())
        ->setTitle('Check-in')
        ->addCheck(Auth::requireLogin())
        ->addCheck(Auth::requireState(Vector {UserState::Confirmed}));
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $url = "http://chart.apis.google.com/chart?cht=qr&chs=150x150&chld=H|0&chl=".$user->getEmail();

    return
      <div>
        <h3>Check-in <small>{$user->getEmail()}</small></h3>
          <p>Use this code to quickly check-in at the event.</p>
          <img class="img-responsive center-block" src={$url}/>
          <a class="btn btn-primary" style="margin-top: 20px;" href={DashboardController::getPath()}>Back</a>
      </div>;
  }
}