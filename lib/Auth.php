<?hh
class Auth {
  public static function login(
    string $username,
    string $password
  ): bool {
    $user = User::genByUsername($username);
    if(!$user) {
      $user = User::genByEmail($username);
    }
    if ($user && hash_equals($user->getPassword(), crypt($password, $user->getPassword()))) {
      Session::create($user);
      return true;
    }
    return false;
  }
  
  public static function loginWithCookie(): bool {
    
    // Get the remember me cookie
    $cookie = isset($_COOKIE['remember_me']) ? $_COOKIE['remember_me'] : null;
    if(!$cookie) {
      return false;
    }
    
    // Parse the cookie
    list ($user_id, $token, $hash) = explode(':', $cookie);
    if ($hash !== hash('sha256', $user_id . ':' . $token)) {
      return false;
    }
    
    // Fail if there's no token in the cookie
    if(empty($token)) {
      return false;
    }
    
    $user = User::genByIDAndToken((int)$user_id, $token);
    
    if($user) {
      // User with token exists, setup the session
      Session::create($user);
      return true;
    }
    
    // No user exists, fail login
    return false;
  }
  
  public static function rememberMe(): void {
    $user = Session::getUser();
    
    // Generate a random string as the token
    $random_token_string = hash('sha256', mt_rand());
    
    // Create the cookie
    $cookie_string_first_part = $user->getID() . ':' . $random_token_string;
    $cookie_string_hash = hash('sha256', $cookie_string_first_part);
    $cookie_string = $cookie_string_first_part . ':' . $cookie_string_hash;
    
    // Set the cookie to 14 days in the future
    setcookie('remember_me', $cookie_string, time() + (3600 * 24 * 14), '/');
    
    // Set the user token in the database
    $user->setToken($random_token_string);
  }

  public static function logout(): void {
    Session::destroy();
    setcookie('remember_me', false, time() - (3600 * 24 * 3650), '/');
  }

  public static function verifyStatus(?array $status): void {
    // Null status array requires no minimum member status
    if(!$status) {
      return;
    }

    // No actice session, so no user is logged in.
    if(!Session::isActive()) {
      Flash::set('error', 'You must be logged in to view this page');
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Route::redirect('/login');
    }

    // Check the users's status against the permitted status
    $user = Session::getUser();
    if(!in_array($user->getStatusID(), $status)) {
      Flash::set('error', 'You do not have permission to view this page');
      Route::redirect('/dashboard');
    }

    return;
  }

  public static function verifyRoles(?array $roles): void {
    // Null roles array requires no specific roles
    if(!$roles) {
      return;
    }

    // No actice session, so no user is logged in.
    if(!Session::isActive()) {
      Flash::set('error', 'You must be logged in to view this page');
      Flash::set('redirect', $_SERVER['REQUEST_URI']);
      Route::redirect('/login');
    }

    // If the intersection of the user's roles and the required roles is empty,
    // the user does not have any of the required roles to view this page
    $user = Session::getUser();
    $intersection = array_intersect($roles, $user->getRoles());
    if(empty($intersection)) {
      Flash::set('error', 'You do not have the required roles to access this page');
      Route::redirect('/dashboard');
    }
  }

  public static function requestPasswordReset(string $username): bool {
    $user = User::genByUsername($username);
    if(!$user) {
      return false;
    }

    $resetHash = sha1(uniqid(mt_rand(), true));
    $user->setPasswordReset($resetHash);

    Email::send(
      $user->getEmail(),
      'Nucleus password reset',
      'To reset your password, follow this link:
       http://nucleus.example.com/password?token=' . $resetHash
    );

    return true;
  }
}
