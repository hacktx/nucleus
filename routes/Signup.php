<?hh

class Signup {

  public static function get(): :xhp {

    if(Session::isActive()) {
      $user = Session::getUser();
      if($user->isMember()) {
        header('Location: dashboard.php');
      } else {
        header('Location: apply.php');
      }
    }

    return
      <div class="well col-md-4 col-md-offset-4">
        <form method="post" action="/signup">
          <div class="form-group">
            <label>Username</label>
            <input type="text" class="form-control" name="uname" placeholder="Username" />
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password" />
          </div>
          <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" class="form-control" name="password2" placeholder="Confirm password" />
          </div>
          <div class="form-group">
            <label>Email</label>
            <input type="email" class="form-control" name="email" placeholder="Email" />
          </div>
          <div class="form-group">
            <label>First Name</label>
            <input type="text" class="form-control" name="fname" placeholder="First Name" />
          </div>
          <div class="form-group">
            <label>Last Name</label>
            <input type="text" class="form-control" name="lname" placeholder="Last Name" />
          </div>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>;
  }

  public static function post(): void {
    if($_POST['password'] != $_POST['password2']) {
      header('Location /signup');
      return;
    }
    $user = User::create(
      $_POST['uname'],
      $_POST['password'],
      $_POST['email'],
      $_POST['fname'],
      $_POST['lname']
    );
    if(!$user) {
      header('Location: /signup');
      return;
    }
    Session::create($user);
    header('Location: /dashboard');
  }
}
