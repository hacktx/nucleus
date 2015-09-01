<?hh

class OAuthCallbackController extends BaseController {
  public static function getPath(): string {
    return '/oauth/callback';
  }

  public static function get(): void {
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

      $_SESSION['access_token'] = $token;

      Route::redirect(ConfirmUserController::getPath());
    } catch (Exception $e) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again.');
      Route::redirect(FrontpageController::getPath());
    }
  }
}
