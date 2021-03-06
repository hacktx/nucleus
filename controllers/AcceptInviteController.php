<?hh

class AcceptInviteController extends BaseController {
  public static function getPath(): string {
    return '/invite/accept';
  }

  public static function getConfig(): ControllerConfig {
    return
      (new ControllerConfig())
        ->addCheck(Auth::requireLogin())
        ->addCheck(Auth::requireState(Vector {UserState::Accepted}));
  }

  public static function get(): :xhp {
    return <nucleus:accept-invite user={Session::getUser()} />;
  }

  public static function post(): void {
    $user = Session::getUser();
    $post_params = getPOSTParams();

    // The user has denied their invite
    if (array_key_exists('deny', $post_params)) {
      UserMutator::update($user->getID())
        ->setState(UserState::Rejected)
        ->save();
      Flash::set(
        Flash::SUCCESS,
        "Your invitation was successfully declined.",
      );
      Route::redirect(DashboardController::getPath());
    }

    // An accept wasn't sent, error
    if (!array_key_exists('accept', $post_params)) {
      http_response_code(400);
      Flash::set(Flash::SUCCESS, "Something went wrong, please try again");
      Route::redirect(self::getPath());
    }

    // Make sure the Code of Conduct is accepted
    if (!array_key_exists('coc', $post_params)) {
      Flash::set(
        Flash::ERROR,
        "The MLH Code of Conduct must be accepted before you can confirm your invitation",
      );
      Route::redirect(self::getPath());
    }

    // The user is uploading a resume
    if ($_FILES['resume']['name'] !== "") {
      // Make sure the resume is a PDF
      $file_type =
        pathinfo(basename($_FILES["resume"]["name"]), PATHINFO_EXTENSION);
      if ($file_type != "pdf") {
        http_response_code(400);
        Flash::set(Flash::ERROR, "Résumé must be in pdf format");
        Route::redirect(self::getPath());
      }

      $upload_dir = "uploads/".$user->getID();

      // Create the upload directory for the user
      if (!file_exists($upload_dir)) {
        mkdir($upload_dir);
      }

      // Move the file to its final home
      if (!move_uploaded_file(
            $_FILES['resume']['tmp_name'],
            $upload_dir."/resume.pdf",
          )) {
        Flash::set(Flash::ERROR, "Résumé was not uploaded successfully");
        Route::redirect(self::getPath());
      }
    }

    // Get the demographic information
    $data = Map {
      'gender' => $user->getGender(),
      'school' => $user->getSchool(),
      'major' => $user->getMajor(),
    };

    if (isset($_POST['first-hackathon']) &&
        $_POST['first-hackathon'] !== "optout") {
      $data['is_first_hackathon'] =
        $_POST['first-hackathon'] === "yes" ? true : false;
    }

    if (array_key_exists('year', $post_params)) {
      if ($_POST['year'] !== "Select one") {
        $data['year'] = $_POST['year'];
      }
    }

    if (array_key_exists('race', $post_params)) {
      $races = Vector {};
      foreach ($_POST['race'] as $race) {
        if ($race === "other") {
          $races[] = $_POST['otherrace'];
          continue;
        }
        $races[] = $race;
      }

      $data['race'] = $races;
    }

    // Send demographic information to keen
    $client = KeenIO\Client\KeenIOClient::factory(
      [
        'projectId' => Config::get('Keen')['project_id'],
        'writeKey' => Config::get('Keen')['write_key'],
        'readKey' => Config::get('Keen')['read_key'],
      ],
    );
    $client->addEvent('confirmation', $data->toArray());

    // Set the user to confirmed
    UserMutator::update($user->getID())
      ->setState(UserState::Confirmed)
      ->save();

    Flash::set(Flash::SUCCESS, "You've successfully confirmed.");
    Route::redirect(DashboardController::getPath());
  }
}
