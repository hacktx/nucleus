<?hh // strict

class DashboardController extends BaseController {
  public static function getPath(): string {
    return '/dashboard';
  }

  public static function getConfig(): ControllerConfig {
    return
      (new ControllerConfig())
        ->setTitle('Dashboard')
        ->addCheck(Auth::requireLogin());
  }

  public static function get(): :xhp {
    $user = Session::getUser();

    $user_status = $user->getState();
    $status = UserState::getNames()[$user_status];

    $child = null;
    switch ($user_status) {
      case UserState::Pending:
        $child =
          <x:frag>
            <p class="info">
              Acceptances will roll out within ~7 days. If accepted, you will
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
            Sorry, your invite expired! We've extended invites to other applicants, but we'll let you know soon if a spot opens up.
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
              <p>
                Authorization for emergency medical treatment
                {$medical_auth_badge} (
                <a href="/files/medauth_adult.pdf" target="_blank">
                  Download
                </a>)
              </p>
              <input type="file" name="medical-auth" />
              <hr />
              <p>
                Release and indemnification agreement {$release_badge} (
                <a href="/files/release_nonstudent.pdf" target="_blank">
                  Download
                </a>)
              </p>
              <input type="file" name="release" />
            </x:frag>;
        }

        $resume_badge = null;
        if (file_exists('uploads/resumes/'.$user->getLastName().'_'.$user->getFirstName().'.pdf')) {
          $resume_badge = <span class="badge">&#10004;</span>;
        }

        $child =
          <form
            action={self::getPath()}
            method="post"
            enctype="multipart/form-data">
            <h3>You successfully accepted your invitation!</h3>
            <br />
            <a class="btn btn-primary" href={CheckinController::getPath()}>Check-In</a>
            
            <a class="btn btn-primary" href="http://freetailhackers.com/design-hacks/info">Info Packet</a>
            <br /><br />
            <h4>
              If you need to upload any additional forms or files, you can do
              that here
            </h4>
            <br />
            <p>Resume {$resume_badge}</p>
            <input type="file" name="resume" />
            <p>Portfolio Website</p>
            <input type="text" name="portfolio" value={$user->getPersonalWebsite()} />
            {$extra}
            <br /><br />
            <button type="submit" class="btn btn-default">Update</button>
          </form>;
        break;
      case UserState::Rejected:
        $child =
          <p class="info">
            We hope to see you at a future event!<br /><br />
            Keep up to date by following us on 
            <a href="http://twitter.com/FreetailHackers">Twitter</a> 
            or <a href="http://facebook.com/FreetailHackers">Facebook</a>.
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
    $post = getPOSTParams();

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

      $upload_dir = "uploads/resumes";

      // Create the upload directory for the user
      if (!file_exists($upload_dir)) {
        mkdir($upload_dir);
      }

      // Move the file to its final home
      if (!move_uploaded_file(
            $files['resume']['tmp_name'],
            $upload_dir."/".$user->getLastName()."_".$user->getFirstName().".pdf",
          )) {
        Flash::set(Flash::ERROR, "Résumé was not uploaded successfully");
        Route::redirect(self::getPath());
      }
    }

    if($post->contains('portfolio')) {
      UserMutator::update($user->getID())
        ->setPersonalWebsite((string) $post['portfolio'])
        ->save();
    }

    $flagged = $user->getRoles()->contains(UserRole::Flagged);

    // Upload a medical release form
    if ($flagged &&
        $files->contains('medical-auth') &&
        $files['medical-auth']['name'] !== "") {
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
    if ($flagged &&
        $files->contains('release') &&
        $files['release']['name'] !== "") {
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

    Flash::set(Flash::SUCCESS, "Profile updated successfully!");
    Route::redirect(self::getPath());
  }
}
