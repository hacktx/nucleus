<?hh

class Index {
  public static function get(): :body {
    # If a user is logged in, redirect them to where they belong
    if(Session::isActive()) {
      $user = Session::getUser();
      if($user->isMember()) {
        header('Location: /dashboard');
      } else {
        header('Location: /apply');
      }
    }

    return 
      <body>
        <div id="crest"></div>
        <div id="login">
          <a id="signin" class="btn btn-default" href="/login">Login</a>
          <a id="signup" class="btn btn-default" href="/signup">Sign Up</a>
        </div>
      </body>;
  }
}
