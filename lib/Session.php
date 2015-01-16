<?php

class Session {
  public static function init() {
    if (isset($_SESSION['user'])) {
      return User::genByUsername($_SESSION['user']);
    }

    $cookie = Cookie::find('id');
    if ($cookie) {
      $user = User::genByToken($cookie->getValue());
      $_SESSION['user'] = $user->getUsername();
      return $user;
    }

    return null;
  }

  public static function create(User $user) {
    $_SESSION['user'] = $user->getUsername();
  }

  public static function destroy() {
    if (isset($_SESSION['user'])) {
      unset($_SESSION['user']);
    }
  }

  public static function isActive() {
    return isset($_SESSION['user']);
  } 
}
