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
    $email = Session::getUser();

    return
      <div>
        <h3>QR Code</h3>
          <p>Use this code to quickly check-in at the event!</p>
          <img class="img-responsive center-block" src="http://chart.apis.google.com/chart?cht=qr&chs=150x150&chl={$user->getEmail()} &chld=H|0"/>
      </div>;
  }
}