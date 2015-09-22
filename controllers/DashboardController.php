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
        $extra = null;
        if ($user->getRoles()->contains(UserRole::Flagged)) {
          $medical_auth_badge = null;
          if (file_exists('uploads/'.$user->getID().'/medical-auth.pdf')) {
            $medical_auth_badge = <span class="badge">&#10004;</span>;
          }

          $release_badge = null;
          if (file_exists('uploads/'.$user->getID().'/release.pdf')) {
            $release_badge = <span class="badge">&#10004;</span>;
          }

          $extra =
            <x:frag>
              <hr />
              <p>Authorization for emergency medical treatment {$medical_auth_badge}</p>
              <input type="file" name="medical-auth" />
              <hr />
              <p>Release and indemnification agreement {$release_badge}</p>
              <input type="file" name="release" />
            </x:frag>;
        }

        $resume_badge = null;
        if (file_exists('uploads/'.$user->getID().'/resume.pdf')) {
          $resume_badge = <span class="badge">&#10004;</span>;
        }

        $child =
          <form
            action={self::getPath()}
            method="post"
            enctype="multipart/form-data"
            class="footer dropzone"
            id="my-awesome-dropzone">
            <h3>You successfully accepted your invitation!</h3>
            <br />
            <h4>
              If you need to upload any additional forms or files, you can do
              that here
            </h4>
            <br />
            <p>Resume {$resume_badge}</p>
            <input type="file" name="resume" />
            {$extra}
            <br />
            <button type="submit" class="btn btn-default">Upload</button>
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

    // Upload a resume
    if ($files->contains('resume') && $files['resume']['name'] !== "") {
      // Make sure the resume is a PDF
      $file_type =
        pathinfo(basename($files["resume"]["name"]), PATHINFO_EXTENSION);
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
            $files['resume']['tmp_name'],
            $upload_dir."/resume.pdf",
          )) {
        Flash::set(Flash::ERROR, "Résumé was not uploaded successfully");
        Route::redirect(self::getPath());
      }
    }

    $flagged = $user->getRoles()->contains(UserRole::Flagged);

    // Upload a medical release form
    if ($flagged && $files->contains('medical-auth') && $files['medical-auth']['name'] !== "") {
      // Make sure the file is a PDF
      $file_type = pathinfo(
        basename($files["medical-auth"]["name"]),
        PATHINFO_EXTENSION,
      );
      if ($file_type != "pdf") {
        http_response_code(400);
        Flash::set(
          Flash::ERROR,
          "Medical Authorization form must be in pdf format",
        );
        Route::redirect(self::getPath());
      }

      $upload_dir = "uploads/".$user->getID();

      // Create the upload directory for the user
      if (!file_exists($upload_dir)) {
        mkdir($upload_dir);
      }

      // Move the file to its final home
      if (!move_uploaded_file(
            $files['medical-auth']['tmp_name'],
            $upload_dir."/medical-auth.pdf",
          )) {
        Flash::set(
          Flash::ERROR,
          "Medical Authorization form was not uploaded successfully",
        );
        Route::redirect(self::getPath());
      }
    }

    // Upload a release
    if ($flagged && $files->contains('release') && $files['release']['name'] !== "") {
      // Make sure the file is a PDF
      $file_type =
        pathinfo(basename($files["release"]["name"]), PATHINFO_EXTENSION);
      if ($file_type != "pdf") {
        http_response_code(400);
        Flash::set(Flash::ERROR, "Release form must be in pdf format");
        Route::redirect(self::getPath());
      }

      $upload_dir = "uploads/".$user->getID();

      // Create the upload directory for the user
      if (!file_exists($upload_dir)) {
        mkdir($upload_dir);
      }

      // Move the file to its final home
      if (!move_uploaded_file(
            $files['release']['tmp_name'],
            $upload_dir."/release.pdf",
          )) {
        Flash::set(
          Flash::ERROR,
          "Release form was not uploaded successfully",
        );
        Route::redirect(self::getPath());
      }
    }

    Flash::set(Flash::SUCCESS, "Files uploaded successfully!");
    Route::redirect(self::getPath());
  }
}
