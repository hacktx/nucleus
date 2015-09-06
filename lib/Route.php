<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {

    // Get the auto-generated URI Map
    $routes = require('build/URIMap.php');

    // Match the path
    foreach($routes as $route_path => $controller_name) {
      $uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
      if(preg_match(
        "@^$route_path$@i",
        "$uri",
        $_SESSION['route_params'])
      ) {
        $controller = new $controller_name();
        invariant($controller instanceof BaseController);

        Auth::verifyStatus($controller->getConfig()->getUserState());
        Auth::verifyRoles($controller->getConfig()->getUserRoles());

        $content = $controller::$method();
        if(is_object($content) && is_a($content, :xhp::class)) {
          Render::go($content, $controller_name);
        } elseif (
          (is_array($content)) ||
          (is_object($content) && is_a($content, Map::class))
        ) {
          header('Content-Type: application/json');
          print json_encode($content, JSON_PRETTY_PRINT);
        }

        return;
      }
    }

    // No path was matched
    Render::go(FourOhFourController::get(), 'FourOhFourController');
  }

  public static function redirect(string $path): void {
    header('Location: ' . $path);
    exit();
  }
}
