<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {
    $routes = Map {
      '/' => 'Index',
      '/apply' => 'Apply',
      '/signup' => 'Signup',
      '/login' => 'Login'
    };

    if($routes->contains($path)) {
      # Get the output from the route
      require_once('routes/' . $routes[$path] . '.php');
      $controller = new $routes[$path];
      $method = strtolower($method);
      $output = $controller::$method();
    } else {
      # Route does not exist, 404
      require_once('routes/FourOhFour.php');
      $output = FourOhFour::get();
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
        {$output}
      </x:frag>;
  }
}
