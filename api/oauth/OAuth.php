<?hh

class OAuth {

  public static string $dsn = '';

  private OAuth2\Server $server;

  public function __construct(): void {
    $storage = new OAuth2\Storage\Pdo(
      array(
        'dsn' => self::$dsn,
        'username' => DB::$user,
        'password' => DB::$password
      )
    );
    $this->server = new OAuth2\Server($storage);
    $this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
    $this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
  }

  public function getOAuthServer(): OAuth2\Server {
    return $this->server;
  } 
}
