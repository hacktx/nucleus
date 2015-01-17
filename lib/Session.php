<?hh

class Session {
  public static function init(): ?User {
    session_start();

    if (isset($_SESSION['user'])) {
      return User::genByUsername($_SESSION['user']);
    }

    $cookie = Cookie::find('id');
    if ($cookie) {
      $user = User::genByToken($cookie->getValue());
      $_SESSION['user'] = $user;
      return $user;
    }

    return null;
  }

  public static function create(User $user): void {
    $_SESSION['user'] = $user;
  }

  public static function destroy(): void {
    if (isset($_SESSION['user'])) {
      unset($_SESSION['user']);
    }
  }

  public static function isActive(): bool {
    return isset($_SESSION['user']);
  }

  public static function getUser(): ?User {
    return $_SESSION['user'];
  }
}
