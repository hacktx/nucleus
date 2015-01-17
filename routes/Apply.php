<?hh

class Apply {
  public static function get(): :xhp {
    # You must be authed to view the application
    if(!Session::isActive()) {
       header('Location: login.php');
    }

    # Members have nothing to do here
    $user = Session::getUser();
    if($user->isMember()) {
      header('Location: dashboard.php');
    }

    $application = Application::genByUser($user);
 
    return
      <body>
        <form method='post' action='/apply'>
        </form>
      </body>;
  }
}
