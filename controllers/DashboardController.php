<?hh // strict

class DashboardController extends BaseController {
  public static function getPath(): string {
    return '/dashboard';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserState(
      array(
        UserState::Pending,
        UserState::Accepted,
        UserState::Waitlisted,
        UserState::Rejected,
        UserState::Confirmed,
      ),
    );
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $user_status = $user->getStatus();
    $status = UserState::getNames()[$user_status];

    $child = null;
    switch ($user_status) {
      case UserState::Pending:
        $child =
          <x:frag>
            <p class="info">
              Acceptances will roll out in ~7 days. If accepted, you will
              receive a confirmation email at {$user->getEmail()} with further
              instructions.
            </p>
            <div class="footer">
              <p>
                {"Can't make it?"}
                <a href={DeleteAccountController::getPath()}>
                  Cancel My Application
                </a>
              </p>
            </div>
          </x:frag>;
        break;
      case UserState::Accepted:
        $child =
          <p class="info">
            You received an invitation! Respond to it
            <a href={AcceptInviteController::getPath()}>here</a>
          </p>;
        break;
      case UserState::Waitlisted:
        $child =
          <p class="info">
            Keep an eye on your email as we send out more invites! Invites are
            awarded on a first-come-first-serve basis.
          </p>;
        break;
      case UserState::Confirmed:
        $child =
          <form
            action={self::getPath()}
            class="footer dropzone"
            id="my-awesome-dropzone">
            <h3>You successfully accepted your invitation!</h3>
            <br />
            <p>
              Still need to upload your resume or want to upload a new one? Do
              it here! PDFs only.
            </p>
            <script src="/js/dropzone.min.js"></script>
          </form>;
        break;
      case UserState::Rejected:
        $child =
          <p class="info">
            We hope to see you next year! If you are interested in
            volunteering, you can sign up
            <a href="http://goo.gl/forms/8Ygo93YMXS">here</a>
          </p>;
        $status = null;
        break;
    }

    return
      <nucleus:dashboard name={$user->getFirstName()} status={$status}>
        {$child}
      </nucleus:dashboard>;
  }

  public static function post(): void {
    $user = Session::getUser();

    if ($user->getStatus() !== UserState::Confirmed) {
      Flash::set(Flash::ERROR, "You don't have permission to do that");
      Route::redirect(self::getPath());
    }

    $files = getFILESParams();
    if ($files['file']['name'] === "") {
      Flash::set(Flash::ERROR, "No file was provided");
      Route::redirect(self::getPath());
    }

    // Make sure the resume is a PDF
    $file_type =
      pathinfo(basename($files["file"]["name"]), PATHINFO_EXTENSION);
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
          $files['file']['tmp_name'],
          $upload_dir."/resume.pdf",
        )) {
      Flash::set(Flash::ERROR, "Résumé was not uploaded successfully");
      Route::redirect(self::getPath());
    }

    Flash::set(Flash::SUCCESS, "Résumé uploaded successfully!");
    Route::redirect(self::getPath());
  }
}
