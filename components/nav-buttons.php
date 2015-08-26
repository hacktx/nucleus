<?hh

final class :nucleus:nav-buttons extends :x:element {
  attribute
    User user,
    string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $nav_buttons =
      <ul class="nav navbar-nav">
        <li class={$controller === 'DashboardController' ? 'active' : ''}>
          <a href="/dashboard">Dashboard</a>
        </li>
      </ul>;

    return $nav_buttons;
  }
}
