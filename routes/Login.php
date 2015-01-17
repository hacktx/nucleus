<?hh

class Login {
  public static function get(): :body {
    return
      <body>
        <form method="post" action="/login">
          <input type="text" name="username" placeholder="Username" />
          <input type="password" name="password" placeholder="Password" />
          <input type="checkbox" name="remember" placeholder="Remember" />
          <button type="submit">Submit</button>
        </form>
      </body>;
  }

  public static function post(): void {
    $remember = $_POST['remember'] === null ? false : true;
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
