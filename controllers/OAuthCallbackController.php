<?hh

class OAuthCallbackController extends BaseController {
  public static function getPath(): string {
    return '/oauth/callback';
  }

  public static function get(): void {
    if (empty($_GET['state']) || ($_GET['state'] !== Flash::get('oauth2state'))) {
      Flash::set(Flash::ERROR, 'Invalid OAuth state');
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

      if(!$user) {
        $user = User::create($mlh_user);

        if(!$user) {
          Flash::set(Flash::ERROR, 'User creation failed');
          Route::redirect('/');
          return;
        }
      }

      Session::create($user);
      Route::redirect(DashboardController::getPath());
    } catch (Exception $e) {
      Flash::set(Flash::ERROR, 'Invalid OAuth state');
      Route::redirect('/');
    }
  }
}
