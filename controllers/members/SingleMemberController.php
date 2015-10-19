<?hh // strict

class SingleMemberController extends BaseController {
  public static function getPath(): string {
    return '/members/(?<id>\d+)';
  }

  public static function getConfig(): ControllerConfig {
    return
      (new ControllerConfig())
        ->addCheck(Auth::requireLogin())
        ->addCheck(
          Auth::requireRoles(
            Vector {UserRole::Superuser, UserRole::Organizer},
          ),
        );
  }

  public static function get(): :xhp {
    $id = (int) self::getPathParam('id');
    $user = User::load($id);
    if (!$user) {
      Flash::set(Flash::ERROR, "User not found");
      Route::redirect(MembersController::getPath());
      invariant(false, "");
    }

    $roles = Vector {};
    foreach ($user->getRoles() as $role) {
      $roles[] = <span>{$role}&nbsp;</span>;
    }

    $age =
      (new DateTime($user->getBirthday()->format('Y-m-d H:i:s')))
        ->diff(new DateTime('today'))
        ->y;

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFirstName().' '.$user->getLastName()}</h1>
          </div>
          <p>Status: {UserState::getNames()[$user->getState()]}</p>
          <p>School: {$user->getSchool()}</p>
          <p>Major: {$user->getMajor()}</p>
          <p>
            Email:
            <a href={'mailto:'.$user->getEmail()} target="_blank">
              {$user->getEmail()}
            </a>
          </p>
          <p>Age: {$age}</p>
          <p>Attributes: {$roles}</p>
        </div>
      </div>;
  }
}
