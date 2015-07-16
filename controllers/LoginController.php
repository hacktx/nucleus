<?hh

class LoginController extends BaseController {
  public static function getPath(): string {
    return '/login';
  }

  public static function get(): void {
    $provider = new League\OAuth2\Client\Provider\MLH([
      'clientId'      => Config::get('mlh')['id'],
      'clientSecret'  => Config::get('mlh')['secret'],
      'redirectUri'   => Config::get('mlh')['redirect'],
    ]);

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    Flash::set('oauth2state', $provider->getState());
    Route::redirect($authUrl);
  }
}
