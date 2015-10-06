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
      <nucleus:batch-modify>
        {$states}
      </nucleus:batch-modify>;
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
