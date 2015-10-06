<?hh

class ControllerConfig {
  private string $title = '';
  private array<UserState> $user_state = array();
  private array<UserRole> $user_roles = array();

  public function setUserState(array<UserState> $states): this {
    $this->user_state = $states;
    return $this;
  }

  public function getUserState(): array<UserState> {
    return $this->user_state;
  }

  public function setUserRoles(array<UserRole> $roles): this {
    $this->user_roles = $roles;
    return $this;
  }

  public function getUserRoles(): array<UserRole> {
    return $this->user_roles;
  }

  public function setTitle(string $title): this {
    $this->title = $title;
    return $this;
  }

  public function getTitle(): string {
    return $this->title;
  }
}
