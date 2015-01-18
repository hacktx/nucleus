<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {
    $routes = Map {
      '' => 'Index',
      'apply' => 'Apply',
      'signup' => 'Signup',
      'login' => 'Login',
      'review' => 'Review',
      'dashboard' => 'Dashboard',
      'members' => 'Members',
      'events/admin' => 'EventsAdmin'
    };

    $path = trim($path, '/');

    if($routes->contains($path)) {
      # Get the output from the route
      require_once('routes/' . $routes[$path] . '.php');
      $controller = new $routes[$path];
      $output = $controller::$method();
    } else {
      # Route does not exist, 404
      require_once('routes/FourOhFour.php');
      $output = FourOhFour::get();
    }

    $login = <li><a href="/login">Login</a></li>;

    # User dropdown if there's an active session
    $user = Session::getuser();
    if($user) {
      $login =
        <li>
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{$user->getUsername()} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/login?action=logout">Logout</a></li>
            <li><a href="/login?action=refresh">Refresh Permissions</a></li>
          </ul>
        </li>;
    }

    # Show the dashboard link if there's an active session
    if(Session::isActive()) {
      $nav_buttons =
        <ul class="nav navbar-nav">
          <li class={$path === 'dashboard' ? 'active' : ''}>
            <a href="/dashboard">Dashboard</a>
          </li>
        </ul>;
    }

    # Applicants get a link to the application page
    if($user && $user->isApplicant()) {
      $nav_buttons->appendChild(
        <li class={$path === 'apply' ? 'active' : ''}>
          <a href="/apply">Apply</a>
        </li>
      );
    }

    # Admins and Reviewers can review applications
    if($user && ($user->isAdmin() || $user->isReviewer())) {
      $nav_buttons->appendChild(
        <li class={$path === 'review' ? 'active' : ''}>
          <a href="/review">Review</a>
        </li>
      );
    }

    # Admins get member management
    if($user && $user->isAdmin()) {
      $nav_buttons->appendChild(
        <li class={$path === 'members' ? 'active' : ''}>
          <a href="/members">Members</a>
        </li>
      );
      $nav_buttons->appendChild(
        <li class={$path === 'events/admin' ? 'active' : ''}>
          <a href="/events/admin">Events</a>
        </li>
      );
    }

    # Render all the things
    print
      <x:frag>
        <head>
          <meta charset="UTF-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1" />
          <title>Omega | Texas LAN</title>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
          <link rel="stylesheet" type="text/css" href="/css/styles.css" />
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        </head>
        <body>
          <nav class="navbar navbar-default navbar-static-top">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="/">Omega</a>
              </div>
              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                {$nav_buttons}
                <ul class="nav navbar-nav navbar-right">
                  {$login}
                </ul>
              </div>
            </div>
          </nav>
          <div class="container">
            {$output}
          </div>
        </body>
      </x:frag>;
  }
}
