<?hh

class Dashboard {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: index.php');
    }

    # Redirect applicants to application page
    $user = Session::getUser();
    if (!$user->isMember()) {
      header('Location: apply.php');
    }
    
    return
      <h1>Dashboard</h1>;
  }
}
