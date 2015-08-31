<?hh

class DeleteAccountController extends BaseController {
  public static function getPath(): string {
    return '/user/delete';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserState(array(
        UserState::Pending,
        UserState::Accepted,
        UserState::Waitlisted,
        UserState::Rejected
      ));
  }

  public static function get(): :xhp {
    return
      <x:frag>
        <div class="col-md-12 text-center">
          <h1>Are you sure you want to delete your account?</h1>
          <h2>This is permanent and cannot be undone.</h2>
          <form action={self::getPath()} method="post"><button type="submit" class="btn btn-danger">Delete my account</button></form>
          <a href={DashboardController::getPath()} class="btn btn-default" role="button">Cancel</a>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    $user = Session::getUser();
    $user->delete();

    Flash::set(Flash::SUCCESS, "Your account was successfully deleted");
    Route::redirect(FrontpageController::getPath());
  }
}
