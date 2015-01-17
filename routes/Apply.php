<?hh

class Apply {
  public static function get(): :body {
    # You must be authed to view the application
    if(!Session::isActive()) {
       header('Location: /login');
    }

    # Members have nothing to do here
    $user = Session::getUser();
    if($user->isMember()) {
      header('Location: /dashboard');
    }

    //$application = Application::genByUser($user);
 
    return
      <body>
        <form method='post' action='/apply'>
          <input type='text' />
        </form>
      </body>;
  }
}
