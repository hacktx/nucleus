<?hh

class OAuth {

  public static string $dsn = '';

  private OAuth2\Server $server;

  public function __construct(): void {
    // Setup storage
    $storage = new OAuth2\Storage\Pdo(
      array(
        'dsn' => self::$dsn,
        'username' => DB::$user,
        'password' => DB::$password
      )
    );

    // Setup scopes
    $defaultScope = 'userprofile';
    $supportedScopes = array(
      'userprofile'
    );
    $memory = new OAuth2\Storage\Memory(array(
      'default_scope' => $defaultScope,
      'supported_scopes' => $supportedScopes
    ));
    $scopeUtil = new OAuth2\Scope($memory);

    // OAuth server confi
    $config = array(
      'enforce_state' => false
    );

    // Configure OAuth server
    $this->server = new OAuth2\Server($storage, $config);
    $this->server->setScopeUtil($scopeUtil);
    $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
    $this->server->addGrantType(new OAuth2\GrantType\RefreshToken($storage));
  }

  public function getOAuthServer(): OAuth2\Server {
    return $this->server;
  } 
}
