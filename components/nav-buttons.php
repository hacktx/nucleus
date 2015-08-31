<?hh

final class :nucleus:nav-buttons extends :x:element {
  attribute
    User user,
    string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $roles = $user->getRoles();

    if (empty($roles)) {
      return <ul class="nav navbar-nav"></ul>;
    }

    $nav_buttons =
      <ul class="nav navbar-nav">
        <li class={$controller === DashboardController::class ? 'active' : ''}>
          <a href={DashboardController::getPath()}>Dashboard</a>
        </li>
      </ul>;

    if (!empty(array_intersect(array(UserRole::Organizer, UserRole::Superuser), $roles))) {
      $nav_buttons->appendChild(
        <li class={$controller === MembersController::class ? 'active' : ''}>
          <a href={MembersController::getPath()}>Members</a>
        </li>
      );
    }

    if (in_array(UserRole::Superuser, $roles)) {
      $nav_buttons->appendChild(
        <li class={$controller === SettingsController::class ? 'active' : ''}>
          <a href={SettingsController::getPath()}>Site Settings</a>
        </li>
      );
    }
    
    return $nav_buttons;
  }
}
