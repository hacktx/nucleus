<?hh

class UserAPI {
  public static function get(): Map {
    $oauth = new OAuth();
    $server = $oauth->getOAuthServer();
    $request = OAuth2\Request::createFromGlobals();
    $response = new OAuth2\Response();
    $scopeRequired = 'userprofile';
    if (!$server->verifyResourceRequest($request, $response, $scopeRequired)) {
      $response->send();
    }

    $token = $server->getAccessTokenData(OAuth2\Request::createFromGlobals());
    $user_id = $token['user_id'];
    $user = User::genByID($user_id);

    if(!$user) {
      return Map {};
    }

    return Map {
      'id' => $user->getID(),
      'username' => $user->getUsername(),
      'email' => $user->getEmail(),
      'firstname' => $user->getFirstName(),
      'lastname' => $user->getLastName()
    };
  }
}
