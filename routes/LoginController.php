<?hh

class LoginController {
  public static function get(): :xhp {
    # Check to see if we're going to perform an action
    $query_params = array();
    parse_str($_SERVER['QUERY_STRING'], $query_params);
    if(isset($query_params['action'])) {
      # Log the user out
      if($query_params['action'] === 'logout') {
        Auth::logout();
        Route::redirect('/');
      }
    }

    if(Session::isActive()) {
      Route::redirect('/dashboard');
    }

    return
      <div class="well col-md-4 col-md-offset-4">
        <form method="post" action="/login">
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="username" placeholder="Username" />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" />
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>;
  }

  public static function post(): void {
    # Make sure all required fields were filled out
    if(!isset($_POST['username']) || !isset($_POST['password'])) {
      Route::redirect('/login');
    }

    # Authenticate
    if(!Auth::login($_POST['username'], $_POST['password'])) {
      Flash::set('error', 'Login failed');
      Route::redirect('/login');
    }

    # Redirect to where we need to go
    $user = Session::getUser();
    if(!$user) {
      Route::redirect('/login');
    } else {
      if(Flash::exists('redirect')) {
        Route::redirect(Flash::get('redirect'));
      }
      Route::redirect('/dashboard');
    }
  }
}
