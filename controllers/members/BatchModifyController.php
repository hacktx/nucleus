<?hh

class BatchModifyController extends BaseController {
  public static function getPath(): string {
    return '/members/batch';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserRoles(
      array(UserRole::Superuser, UserRole::Organizer),
    );
  }

  public static function get(): :xhp {
    $states = Vector {};
    foreach (UserState::getNames() as $value => $name) {
      $states[] = <option value={$name}>{$name}</option>;
    }
    return
      <form method="post">
        <div class="form-inline">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">Move</div>
              <select class="form-control" name="place" id="place">
                <option>First</option>
                <option>Last</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <input
              name="number"
              type="text"
              class="form-control"
              id="number-accept"
              placeholder="600"
            />
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">From</div>
              <select class="form-control" name="from" id="from">
                {$states}
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">To</div>
              <select class="form-control" name="to" id="to">
                {$states}
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="subject">Email Subject</label>
          <input
            type="text"
            class="form-control"
            id="subject"
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
    $from = UserState::getValues()[$_POST['from']];
    $to = UserState::getValues()[$_POST['to']];
    $order = $_POST['place'] === "First" ? "ASC" : "DESC";

    // Get [n] applicants who are in the "from" state
    $query = DB::query(
      "SELECT * FROM users WHERE status=%s ORDER BY created %l LIMIT %i",
      $from,
      $order,
      (int) $_POST['number'],
    );
    $count = DB::count();

    $email_client = new SendGrid(Config::get('SendGrid')['api_key']);

    // Move the [n] applications to the "to" state and email them
    foreach ($query as $row) {
      User::updateStatusByID($to, (int) $row['id']);
      $email = new SendGrid\Email();
      $email->addTo($row['email'])
        ->setFrom("noreply@hacktx.com")
        ->setFromName("Team HackTX")
        ->setSubject($_POST['subject'])
        ->setHtml($_POST['email'])
        ->addSubstitution("%first_name%", array($row['fname']));

      $email_client->send($email);
    }

    Flash::set(
      Flash::SUCCESS,
      $count." members successfully updated",
    );
    Route::redirect(self::getPath());
  }
}
