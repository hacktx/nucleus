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

    $options = Vector {};
    foreach (UserState::getValues() as $name => $value) {
      $options[] = <option>{$name}</option>;
    }

    return
      <form>
        <div class="form-group">
          <label for="number-accept">Number of people to modify</label>
          <input
            type="text"
            class="form-control"
            id="number-accept"
            placeholder="600"
          />
        </div>
        <div class="form-group">
          <label for="email-input">Email Contents</label>
          <textarea class="form-control" rows={3} />
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

    $query = DB::query(
      "SELECT * FROM users WHERE status=%s ORDER BY created LIMIT %i",
      UserState::Pending,
      (int) $_POST['number'],
    );

    foreach ($query as $row) {
      User::updateStatusByID(UserState::Accepted, $row['id']);
      Email::send(
        $row['email'],
        'HackTX Invitation - Action Required',
        $_POST['email'],
      );
    }
  }
}
