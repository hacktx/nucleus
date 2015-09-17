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
    $options = Vector {};
    foreach (UserState::getNames() as $value => $name) {
      $options[] = <option value={$name}>{$name}</option>;
    }
    return
      <form method="post">
        <div class="form-group">
          <label for="user-state">Email all people with state</label>
          <select class="form-control" id="user-state" name="userstate">
            {$options}
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
    $query = DB::query(
      "SELECT * FROM users WHERE status=%s",
      UserState::getValues()[$_POST['userstate']],
    );
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
