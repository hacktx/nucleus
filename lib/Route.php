<?hh // decl

class Route {
  public static function dispatch(string $path, string $method): void {

    # All the routes that exist within the application. This includes required
    # auth levels and access roles to view each page
    $routes = require('build/URIMap.php');
    $tmp = Map {
      '/' => Map {
        'controller' => 'FrontpageController',
        'methods' => 'GET'
      },
      '/signup' => Map {
        'controller' => 'SignupController',
        'methods' => 'GET|POST'
      },
      '/login' => Map {
        'controller' => 'LoginController',
        'methods' => 'GET|POST'
      },
      '/apply' => Map {
        'controller' => 'ApplyController',
        'methods' => 'GET|POST',
        'status' => array(User::Applicant)
      },
      '/review' => Map {
        'controller' => 'ReviewListController',
        'methods' => 'GET',
        'status' => array(User::Member),
        'roles' => array(Roles::Reviewer, Roles::Admin)
      },
      '/review/{id}' => Map {
        'controller' => 'ReviewSingleController',
        'methods' => 'GET|POST',
        'tokens' => Map {
          'id' => '\d+'
        },
        'status' => array(User::Member),
        'roles' => array('reviewer', 'admin')
      },
      '/dashboard' => Map {
        'controller' => 'DashboardController',
        'methods' => 'GET',
        'status' => array(User::Applicant, User::Pledge, User::Member, User::Disabled)
      },
      '/members' => Map {
        'controller' => 'MembersController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin)
      },
      '/events/admin' => Map {
        'controller' => 'EventsAdminController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      },
      '/events/attendance/{id}' => Map {
        'controller' => 'EventAttendanceController',
        'methods' => 'GET',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      },
      '/events/{id}' => Map {
        'controller' => 'EventCheckinController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      },
      '/notify' => Map {
        'controller' => 'NotifyController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
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
