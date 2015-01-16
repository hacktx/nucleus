<?php

class Cookie {
  public static function create($key, $value) {
    setcookie($key, $value, time()+60*60*24*30, '/');
  }

  public static function find($key) {
    if (isset($_COOKIE[$key])) {
      return new Cookie($key, $_COOKIE[$key]);
    }
    return null;
  }

  public static function remove($key) {
    if (isset($_COOKIE[$key])) {
      setcookie($key, null, -1, '/');
      unset($_COOKIE[$key]);
    }
  }

  private function __construct($key, $value) {
    $this->key = $key;
    $this->value = $value;
  }

  public function getValue() {
    return $this->value;
  }

  public function destroy() {
    self::remove($this->key);
  }
}
