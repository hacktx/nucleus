<?hh // decl

use Aura\Router\RouterFactory;

class Route {
  public static function dispatch(string $path, string $method): void {

    # All the routes that exist within the application. This includes required
    # auth levels and access roles to view each page
    $routes = Map {
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
        'roles' => array('reviewer', 'admin')
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
        'status' => array(User::Applicant, User::Pledge, User::Member)
      },
      '/members' => Map {
        'controller' => 'MembersController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin)
      },
      '/feedback' => Map {
        'controller' => 'FeedbackListController',
        'methods' => 'GET',
        'status' => array(User::Member)
      },
      '/feedback/{id}' => Map {
        'controller' => 'FeedbackSingleController',
        'methods' => 'GET|POST',
        'tokens' => Map {
          'id' => '\d+'
        },
        'status' => array(User::Member),
      },
      '/events/admin' => Map {
        'controller' => 'EventsAdminController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      },
      '/events/{id}' => Map {
        'controller' => 'EventAttendanceController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      },
      '/notify' => Map {
        'controller' => 'NotifyController',
        'methods' => 'GET|POST',
        'status' => array(User::Member),
        'roles' => array(Roles::Admin, Roles::Officer)
      }
    };

    # Add the routes to Aura
    $router_factory = new RouterFactory;
    $router = $router_factory->newInstance();
    foreach ($routes as $route_path => $settings) {
      $tokens = array('REQUEST_METHOD' => $settings['methods']);
      if(isset($settings['tokens'])) {
        $tokens = array_merge($tokens, $settings['tokens']->toArray());
      }
      $router->add($route_path, $route_path)
        ->addTokens($tokens)
        ->addValues(array(
          'controller' => $settings['controller'],
          'member_status' => isset($settings['status']) ? $settings['status'] : null,
          'roles' => isset($settings['roles']) ? $settings['roles'] : null
        ));
    }

    # Match the path
    $route = $router->match($path, $_SERVER);
    if($route) {
      # Make sure the user has access to view the page they're trying to
      Auth::verifyStatus(
        isset($route->params['member_status']) ? $route->params['member_status'] : null
      );
      Auth::verifyRoles(
        isset($route->params['roles']) ? $route->params['roles'] : null
      );

      # Set the params in the session for use in the controllers
      $_SESSION['route_params'] = $route->params;

      # Render the page
      $controller = new ($route->params['controller']);
      Render::go($controller::$method(), $route->params['controller']);
    } else {
      # No route detected, 404
      Render::go(FourOhFourController::get(), 'FourOhFourController');
    }

  }

  public static function redirect(string $path): void {
    header('Location: ' . $path);
    exit();
  }
}
