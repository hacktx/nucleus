<?hh // strict

class SingleMemberController extends BaseController {
  public static function getPath(): string {
    return '/members/(?<id>\d+)';
  }

  public static function get(): void {
    $id = (int) self::getPathParam('id');
    $user = User::genByID($id);
    if (!$user) {
      Flash::set(Flash::ERROR, "User not found");
      Route::redirect(MembersController::getPath());
      invariant(false, "");
    }
  }
}
