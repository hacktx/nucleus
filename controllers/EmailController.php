<?hh

class EmailController extends BaseController {
  public static function getPath(): string {
    return '/members/email';
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
          <label for="user-state">Email all people with state</label>
          <select class="form-control" id="user-state" name="userstate">
            <option value="ac">Accepted - Confirmed</option>
            <option value="an">Accepted - Not replied</option>
            <option value="w">Waitlisted</option>
            <option value="r">Rejected</option>
          </select>
        </div>
        <div class="form-group">
          <label for="email-subject">Email Subject</label>
          <input
            type="text"
            class="form-control"
            id="email-subject"
            name="subject"
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
    switch ($_POST['userstate']) {
      case "ac":
        $query =
          DB::query(
            "SELECT * FROM users WHERE id IN (SELECT * FROM roles WHERE role=%s)",
            UserRole::Confirmed,
          );
        break;
      case "an":
        $query =
          DB::query(
            "SELECT * FROM users WHERE status=%s AND id NOT IN (SELECT user_id FROM roles WHERE role=%s OR role=%s)",
            UserState::Accepted,
            UserRole::Denied,
            UserRole::Confirmed,
          );
        break;
      case "w":
        $query = DB::query(
          "SELECT * FROM users WHERE status=%s",
          UserState::Waitlisted,
        );
        break;
      case "r":
        $query = DB::query(
          "SELECT * FROM users WHERE status=%s",
          UserState::Rejected,
        );
        break;
    }

    $count = DB::count();

    $email_client = new SendGrid(Config::get('SendGrid')['api_key']);
    foreach ($query as $row) {
      $email = new SendGrid\Email();
      $email->addTo($row['email'])
        ->setFrom("noreply@hacktx.com")
        ->setFromName("Team HackTX")
        ->setSubject($_POST['subject'])
        ->setHtml($_POST['email'])
        ->addSubstitution("%first_name%", array($row['fname']));

      $email_client->send($email);
    }

    Flash::set(Flash::SUCCESS, $count." emails successfully sent");
    Route::redirect(self::getPath());
  }
}
