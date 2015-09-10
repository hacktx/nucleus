<?hh

class BatchAcceptController extends BaseController {
  public static function getPath(): string {
    return '/members/batch';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserRoles(
      array(UserRole::Superuser, UserRole::Organizer),
    );
  }

  public static function get(): :xhp {
    return
      <form method="post">
        <div class="form-group">
          <label for="number-accept">Number of people to modify</label>
          <input
            name="number"
            type="text"
            class="form-control"
            id="number-accept"
            placeholder="600"
          />
        </div>
        <div class="form-group">
          <label for="email-input">Email Contents</label>
          <textarea class="form-control" rows={3} name="email" />
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>;
  }

  public static function post(): void {
    if (!isset($_POST['numer']) && !isset($_POST['email'])) {
      http_response_code(400);
      Flash::set(Flash::ERROR, "Something went wrong! Please try again.");
      Route::redirect(self::getPath());
    }

    // Get [n] applicants who are pending, in order of creation
    $query = DB::query(
      "SELECT * FROM users WHERE status=%s ORDER BY created LIMIT %i",
      UserState::Pending,
      (int) $_POST['number'],
    );

    $email_client = new SendGrid(Config::get('SendGrid')['api_key']);

    // Set the first [n] as accepted and email them
    foreach ($query as $row) {
      User::updateStatusByID(UserState::Accepted, (int) $row['id']);
      $email = new SendGrid\Email();
      $email->addTo($row['email'])
            ->setFrom("noreply@hacktx.com")
            ->setFromName("Team HackTX")
            ->setSubject("HackTX Invitation - Action Required")
            ->setHtml($_POST['email'])
            ->addSubstitution("%first_name%", array($row['fname']));

      $email_client->send($email);
    }

    // Mark the remaining as waitlisted
    DB::query(
      "UPDATE users SET status=%s WHERE status != %s",
      UserState::Waitlisted,
      UserState::Accepted,
    );

    Flash::set(
      Flash::SUCCESS,
      $_POST['number']." applications successfully accepted",
    );
    Route::redirect(self::getPath());
  }
}
