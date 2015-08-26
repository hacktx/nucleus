<?hh

final class :nucleus:nav-bar extends :x:element {
  attribute
    User user,
    string controller;

  final protected function render(): :nav {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $nav_buttons = null;
    $login = <li><a href="/login">Login</a></li>;
    if($user) {
      $nav_buttons = <nucleus:nav-buttons user={$user} controller={$controller} />;

      # Logout dropdown
      $login = <li><a href="/login?action=logout">Logout</a></li>;
    }

    return
      <nav class="navbar navbar-default navbar-static-top">
        <div class="container-fluid">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/">Nucleus</a>
          </div>
          <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            {$nav_buttons}
            <ul class="nav navbar-nav navbar-right">
              {$login}
            </ul>
          </div>
        </div>
      </nav>;
  }
}
