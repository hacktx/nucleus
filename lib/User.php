<?php

class User {

  public static function create(
    username,
    password,
    email,
    fname,
    lname
  ) {
    DB::insert('users')
      ->fields('username', 'password', 'email', 'fname', 'lname')
      ->values(username, password, email fname, lname);
  }

  public function getUsername() {
    return $this->username;
  }

  public function getPassword() {
    return $this->password;
  }

  public function getEmail() {
    return $this->email;
  }

  public function getToken() {
    return $this->token;
  }

  public function isMember() {
    return $this->isMember;
  }

  public function isAdmin() {
    return $this->isAdmin;
  }

  public static function genByUsername(username) {
    return constructFromQuery('username', username);
  }

  public static function genByEmail(email) {
    return constructFromQuery('email', email);
  }

  public static function genByToken(token) {
    return constructFromQuery('token', token);
  }

  private static function constructFromQuery(field, query) {
    $query = DB::select('*')->from('users')->where(field . '=' . value);
  }
}
