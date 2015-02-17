<?hh

class OAuthAuthorize {
  public static function get(): :xhp {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();

    if (!$server->validateAuthorizeRequest($request, $response)) {
        $response->send();
        die;
    }

    return 
      <div class="well col-md-4 col-md-offset-4">
        <form method="post">
          <h1>Do you authorize this application to view your profile information?</h1>
          <input type="submit" name="authorized" value="yes" />
          <input type="submit" name="authorized" value="no" />
        </form>
      </div>;
  }

  public static function post(): void {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();

    $is_authorized = ($_POST['authorized'] === 'yes');
    $user = Session::getUser();
    $server->handleAuthorizeRequest($request, $response, $is_authorized, $user->getID());
    $response->send();
    die;
  }
}
