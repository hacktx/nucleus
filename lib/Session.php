<?hh

class Session {
  public static function init(): ?User {
    session_start();

    # If there's an active user in the session, refresh it
    if (isset($_SESSION['user'])) {
      $user = User::genByID($_SESSION['user']->getID());
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

  public static function getUser(): User {
    return $_SESSION['user'];
  }
}
