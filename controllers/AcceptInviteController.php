<?hh

class AcceptInviteController extends BaseController {
  public static function getPath(): string {
    return '/invite/accept';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserState(array(UserState::Accepted));
  }

  public static function get(): :xhp {
    return <nucleus:accept-invite user={Session::getUser()} />;
  }

  public static function post(): void {
    $user = Session::getUser();

    // The user has denied their invite
    if (isset($_POST['deny'])) {
      User::updateStatusByID(UserState::Rejected, $user->getID());
      Flash::set(
        Flash::SUCCESS,
        "Your invitation was successfully declined.",
      );
      Route::redirect(DashboardController::getPath());
    }

    // An accept wasn't sent, error
    if (!isset($_POST['accept'])) {
      http_response_code(400);
      Flash::set(Flash::SUCCESS, "Something went wrong, please try again");
      Route::redirect(self::getPath());
    }

    // Make sure the Code of Conduct is accepted
    if (!isset($_POST['coc'])) {
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

    if (isset($_POST['year'])) {
      if ($_POST['year'] !== "Select one") {
        $data['year'] = $_POST['year'];
      }
    }

    if (isset($_POST['race'])) {
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
    User::updateStatusByID(UserState::Confirmed, $user->getID());
    Flash::set(Flash::SUCCESS, "You've successfully confirmed.");
    Route::redirect(DashboardController::getPath());
  }
}
