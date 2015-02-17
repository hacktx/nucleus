<?hh

class OAuthToken {
  public static function post(): void {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();
    die;
  }
}
