<?hh

class LoginController extends BaseController {
  public static function getPath(): string {
    return '/login';
  }

  public static function get(): void {
    $query_params = array();
    parse_str($_SERVER['QUERY_STRING'], $query_params);
    if(isset($query_params['action'])) {
      // Log the user out
      if($query_params['action'] === 'logout') {
        Auth::logout();
        Route::redirect('/');
      }
    }

    $provider = new League\OAuth2\Client\Provider\MLH([
      'clientId'      => Config::get('MLH')['client_id'],
      'clientSecret'  => Config::get('MLH')['client_secret'],
      'redirectUri'   => Config::get('MLH')['redirect'],
    ]);

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    Flash::set('oauth2state', $provider->getState());
    Route::redirect($authUrl);
  }
}
