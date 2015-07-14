<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {

    # All the routes that exist within the application. This includes required
    # auth levels and access roles to view each page
    $routes = require('build/URIMap.php');
    $tmp = Map {
      '/review/{id}' => Map {
        'controller' => 'ReviewSingleController',
        'methods' => 'GET|POST',
        'tokens' => Map {
          'id' => '\d+'
        },
        'status' => array(User::Member),
        'roles' => array('reviewer', 'admin')
      },
      '/settings' => Map {
        'controller' => 'SettingsController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin)
      },
      '/api/users/me' => Map {
        'controller' => 'UserAPI',
        'methods' => 'GET'
      } 
    };

    // Match the path
    foreach($routes as $route_path => $controller_name) {
      if(preg_match(
        "@$route_path@i",
        "$_SERVER[REQUEST_METHOD]$_SERVER[REQUEST_URI]",
        $vars)
      ) {
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
