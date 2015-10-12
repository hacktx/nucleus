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
          <p class="emoticon">D:</p>
          <h3>Want to delete your account?</h3>
          <p class="prompt-open">This is permanent and cannot be undone.</p>
          <form action={self::getPath()} method="post"><button type="submit" class="btn btn-danger">Delete my account</button></form>
          <a href={DashboardController::getPath()} class="btn btn-default" role="button">Cancel</a>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    $user = Session::getUser();
    UserMutator::delete($user->getID());

    Flash::set(Flash::SUCCESS, "Your account was successfully deleted");
    Route::redirect(FrontpageController::getPath());
  }
}
