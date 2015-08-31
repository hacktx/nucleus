<?hh

class OAuthCallbackController extends BaseController {
  public static function getPath(): string {
    return '/oauth/callback';
  }

  public static function get(): :xhp {
    if (empty($_GET['state']) || ($_GET['state'] !== Flash::get('oauth2state'))) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again.');
      Route::redirect('/');
    }

    $provider = new League\OAuth2\Client\Provider\MLH([
      'clientId'      => Config::get('MLH')['client_id'],
      'clientSecret'  => Config::get('MLH')['client_secret'],
      'redirectUri'   => Config::get('MLH')['redirect'],
    ]);

    $token = $provider->getAccessToken('authorization_code', [
      'code' => $_GET['code']
    ]);

    try {
      $mlh_user = $provider->getResourceOwner($token);

      $user = User::genByID($mlh_user->getId());

      if($user) {
        Session::create($user);
        Route::redirect(DashboardController::getPath());
      }

      if(!Settings::get('applications_open')) {
        Flash::set(Flash::ERROR, 'Registration is currently closed. Please check back later!');
        Route::redirect(FrontpageController::getPath());
      }

      $_SESSION['mlh_user'] = $mlh_user;

      return
        <x:frag>
          <div class="col-md-6 col-md-offset-3">
            <p>Please review your information.</p>
            <div class="panel panel-default">
              <div class="panel-body">
                <p>Name:<span class="pull-right">{$mlh_user->getName()}</span></p>
                <p>Email:<span class="pull-right">{$mlh_user->getEmail()}</span></p>
                <p>School:<span class="pull-right">{$mlh_user->getEmail()}</span></p>
                <p>Major:<span class="pull-right">{$mlh_user->getEmail()}</span></p>
                <p>Dietary Restrictions:<span class="pull-right">{$mlh_user->getEmail()}</span></p>
              </div>
            </div>
            <div class="text-right">
              <a href="https://my.mlh.io/edit" class="btn btn-default">Update</a>
              <form action={self::getPath()} method="post" style="display: inline-block;">
                <button type="submit" class="btn btn-primary">Confirm</button>
              </form>
            </div>
          </div>
        </x:frag>;
    } catch (Exception $e) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again.');
      Route::redirect(FrontpageController::getPath());
      invariant(false, "");
    }
  }

  public static function post(): void {
    if (!$_SESSION['mlh_user']) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again');
      Route::redirect(FrontpageController::getPath());
    }

    $user = User::create($_SESSION['mlh_user']);
    unset($_SESSION['mlh_user']);
    if(!$user) {
      Flash::set(Flash::ERROR, 'User creation failed');
      Route::redirect('/');
      return;
    }

    Session::create($user);
    Flash::set(Flash::SUCCESS, "Account successfully created!");
    Route::redirect(DashboardController::getPath());
  }
}
