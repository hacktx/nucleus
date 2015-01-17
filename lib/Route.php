<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {
    $routes = Map {
      '' => 'Index',
      'apply' => 'Apply',
      'signup' => 'Signup',
      'login' => 'Login',
      'review' => 'Review',
      'dashboard' => 'Dashboard'
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

    $user = Session::getuser();
    if($user) {
      $login =
        <li>
          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">{$user->getUsername()} <span class="caret"></span></a>
          <ul class="dropdown-menu" role="menu">
            <li><a href="/login?action=logout">Logout</a></li>
          </ul>
        </li>;
    }

    # Render all the things
    print
      <x:frag>
        <head>
          <meta charset="UTF-8" />
          <meta name="viewport" content="width=device-width, initial-scale=1" />
          <title>Omega | Texas LAN</title>
          <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" />
          <link rel="stylesheet" type="text/css" href="css/styles.css" />
          <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
          <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        </head>
        <body>
          <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container-fluid">
              <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only">Toggle navigation</span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">Omega</a>
              </div>
              <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
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
