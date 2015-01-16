<?hh

class User {
  public function getUsername(): string {
    return $this->username;
  }

  public function getPassword(): string {
    return $this->password;
  }

  public function getEmail(): string {
    return $this->email;
  }

  public function getToken(): ?string {
    return $this->token;
  }

  public function isMember() {
    return $this->isMember;
  }

  public function isAdmin() {
    return $this->isAdmin;
  }

  public static function genByUsername(): User {
    return new User();
  }

  public static function genByEmail(): User {
    return new User();
  }

  public static function genByToken(): User {
    return new User();
  }

  private static function constructFromQuery(): User {
    return new User();
  }
}
