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
      <form method="post" action="/login">
        <input type="text" name="username" placeholder="Username" />
        <input type="password" name="password" placeholder="Password" />
        <input type="checkbox" name="remember" placeholder="Remember" />
        <button type="submit">Submit</button>
      </form>;
  }

  public static function post(): void {
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
