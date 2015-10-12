<?hh

class ConfirmUserController extends BaseController {
  public static function getPath(): string {
    return '/user/confirm';
  }

  public static function get(): :xhp {
    if (!$_SESSION['access_token']) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again.');
      Route::redirect(FrontpageController::getPath());
    }

    if (Session::isActive()) {
      Route::redirect(DashboardController::getPath());
    }

    $provider = new League\OAuth2\Client\Provider\MLH(
      [
        'clientId' => Config::get('MLH')['client_id'],
        'clientSecret' => Config::get('MLH')['client_secret'],
        'redirectUri' => Config::get('MLH')['redirect'],
      ],
    );

    $token = $_SESSION['access_token'];

    try {
      $mlh_user = $provider->getResourceOwner($token);

      $user = User::load($mlh_user->getId());

      if ($user) {
        Session::create($user);
        Route::redirect(DashboardController::getPath());
      }

      $_SESSION['mlh_user'] = $mlh_user;

      return
        <x:frag>
          <div class="col-md-6 col-md-offset-3">
            <h2>Almost There!</h2>
            <p class="prompt-review">Please review your information</p>
            <div class="panel panel-default">
              <div class="panel-body">
                <p>Name:<span class="pull-right">{$mlh_user->getName()}</span></p>
                <p>Email:<span class="pull-right">{$mlh_user->getEmail()}</span></p>
                <p>Graduation:<span class="pull-right">{date("F Y", strtotime($mlh_user->getGraduation()))}</span></p>
                <p>Major:<span class="pull-right">{$mlh_user->getMajor()}</span></p>
                <p>Shirt Size:<span class="pull-right">{$mlh_user->getShirtSize()}</span></p>
                <p>Dietary Restrictions:<span class="pull-right">{$mlh_user->getDietaryRestrictions()}</span></p>
                <p>Birthday:<span class="pull-right">{date("F j, Y", strtotime($mlh_user->getBirthday()))}</span></p>
                <p>Gender:<span class="pull-right">{$mlh_user->getGender()}</span></p>
                <p>Phone Number:<span class="pull-right">{$mlh_user->getPhoneNumber()}</span></p>
                <p>School:<span class="pull-right">{$mlh_user->getSchool()}</span></p>
              </div>
            </div>
            <div class="text-right">
              <a href="https://my.mlh.io/edit" class="btn btn-default">
                UPDATE
              </a>
              <form
                action={self::getPath()}
                method="post"
                style="display: inline-block;">
                <button type="submit" class="btn btn-primary">
                  CONFIRM
                </button>
              </form>
            </div>
          </div>
        </x:frag>;
    } catch (Exception $e) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again.');
      Route::redirect(FrontpageController::getPath());
      invariant(false, "");
    }
  }

  public static function post(): void {
    if (!$_SESSION['mlh_user']) {
      Flash::set(Flash::ERROR, 'Something went wrong! Please try again');
      Route::redirect(FrontpageController::getPath());
    }

    $mlh_user = $_SESSION['mlh_user'];
    $user_id = UserMutator::create()
      ->setID($mlh_user->getId())
      ->setEmail($mlh_user->getEmail())
      ->setFirstName($mlh_user->getFirstName())
      ->setLastName($mlh_user->getLastName())
      ->setGraduation($mlh_user->getGraduation())
      ->setMajor($mlh_user->getMajor())
      ->setShirtSize($mlh_user->getShirtSize())
      ->setDietaryRestrictions($mlh_user->getDietaryRestrictions())
      ->setSpecialNeeds($mlh_user->getSpecialNeeds())
      ->setBirthday($mlh_user->getBirthday())
      ->setGender($mlh_user->getGender())
      ->setPhoneNumber($mlh_user->getPhoneNumber())
      ->setSchool($mlh_user->getSchool())
      ->setState(UserState::Pending)
      ->save();

    $user = User::load($user_id);

    if (!$user) {
      Flash::set(Flash::ERROR, 'User creation failed');
      Route::redirect('/');
      return;
    }

    $client = KeenIO\Client\KeenIOClient::factory(
      [
        'projectId' => Config::get('Keen')['project_id'],
        'writeKey' => Config::get('Keen')['write_key'],
        'readKey' => Config::get('Keen')['read_key'],
      ],
    );
    $user_data = $_SESSION['mlh_user']->toArray();
    $client->addEvent('sign_ups', $user_data);

    unset($_SESSION['mlh_user']);
    unset($_SESSION['access_token']);

    Session::create($user);
    Flash::set(Flash::SUCCESS, "Account successfully created!");
    Route::redirect(DashboardController::getPath());
  }
}
