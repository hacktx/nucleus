<?hh

class Login {
  public static function get(): :xhp {
    parse_str($_SERVER['QUERY_STRING'], $query_params);
    if(isset($query_params['action']) && $query_params['action'] === 'logout') {
      Auth::logout();
      header('Location: /');
    }

    if(Session::isActive()) {
      $user = Session::getUser();
      if($user->isMember()) {
        header('Location: /dashboard');
      } else {
        header('Location: /apply');
      }
    }

    return
      <div class="well col-md-4 col-md-offset-4">
        <form method="post" action="/login">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username" placeholder="Username" />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" />
          </div>
          <div class="checkbox">
            <label>
              <input type="checkbox" name="remember" /> Remember Me
            </label>
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>;
  }

  public static function post(): void {
    # Make sure all required fields were filled out
    if(!isset($_POST['username']) || !isset($_POST['password'])) {
      header('Location: /login');
      return;
    }

    # Authenticate
    $remember = isset($_POST['remember']) && $_POST['remember'];
    Auth::login($_POST['username'], $_POST['password'], $remember);

    # Redirect to where we need to go
    $user = Session::getUser();
    if(!$user) {
      header('Location: /login');
    } elseif ($user->isMember()) {
      header('Location: /dashboard');
    } else {
      header('Location: /apply');
    }
  }
}
