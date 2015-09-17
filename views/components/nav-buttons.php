<?hh // strict

final class :nucleus:nav-buttons extends :x:element {
  attribute User user, string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $roles = $user->getRoles();

    if ($roles->isEmpty()) {
      return <ul class="nav navbar-nav"></ul>;
    }

    $nav_buttons =
      <ul class="nav navbar-nav">
        <li
          class={$controller === DashboardController::class ? 'active' : ''}>
          <a href={DashboardController::getPath()}>Dashboard</a>
        </li>
      </ul>;

    if ($roles->contains(UserRole::Organizer) || $roles->contains(UserRole::Superuser)) {
      $nav_buttons->appendChild(
        <li>
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Members <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li><a href={MembersController::getPath()}>Table</a></li>
            <li><a href={BatchModifyController::getPath()}>Batch Modify</a></li>
            <li><a href={EmailController::getPath()}>Email</a></li>
          </ul>
        </li>
      );
    }

    if ($roles->contains(UserRole::Superuser)) {
      $nav_buttons->appendChild(
        <li
          class={$controller === SettingsController::class ? 'active' : ''}>
          <a href={SettingsController::getPath()}>Site Settings</a>
        </li>
      );

      $nav_buttons->appendChild(
        <li
          class={$controller === VolunteerController::class ? 'active' : ''}>
          <a href={VolunteerController::getPath()}>Volunteers</a>
        </li>
      );
    }

    return $nav_buttons;
  }
}
