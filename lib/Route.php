<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {

    // Get the auto-generated URI Map
    $routes = require('build/URIMap.php');

    // Match the path
    foreach($routes as $route_path => $controller_name) {
      if(preg_match(
        "@$route_path@i",
        "$_SERVER[REQUEST_METHOD]$_SERVER[REQUEST_URI]",
        $_SESSION['route_params'])
      ) {
        echo var_dump($_SESSION['route_params']);
        echo $controller_name;
        die;
        $controller = new $controller_name();
        invariant($controller instanceof BaseController);

        Auth::verifyStatus($controller->getConfig()->getUserState());
        Auth::verifyRoles($controller->getConfig()->getUserRoles());

        $content = $controller::$method();
        if(is_object($content) && is_a($content, :xhp::class)) {
          Render::go($content, $controller_name);
        } elseif (is_object($content) && is_a($content, Map::class)) {
          print json_encode($content);
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
