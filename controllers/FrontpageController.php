<?hh

class FrontpageController {
  public static function getPath(): string {
    return '/';
  }

  public static function get(): :xhp {
    # If a user is logged in, redirect them to where they belong
    if(Session::isActive()) {
      header('Location: /dashboard');
    }

    return
      <div class="col-md-6 col-md-offset-3 masthead">
        <p><a id="signin" class="btn btn-default" role="button" href="/login">Login</a></p>
        <p><a id="signup" class="btn btn-default" role="button" href="/signup">Sign Up</a></p>
      </div>;
  }
}
