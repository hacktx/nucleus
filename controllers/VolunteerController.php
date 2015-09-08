<?hh

class VolunteerController extends BaseController {
  public static function getPath(): string {
    return '/volunteers';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserRoles(array(UserRole::Superuser));
  }

  public static function get(): :xhp {
    $volunteers = DB::query("SELECT * FROM volunteer");

    return <nucleus:volunteer volunteers={new Vector($volunteers)} />;
  }

  public static function post(): void {
    foreach ($_POST as $volunteer) {
      if ($volunteer['name'] == "" && $volunteer['email'] == "") {
        continue;
      }

      // We're creating a user, create the ID
      if (!isset($volunteer['id'])) {
        // Find an ID that hasn't been used
        while (true) {
          $id = substr(md5(microtime()), 0, 6);
          DB::query('SELECT * FROM volunteer WHERE id=%s', $id);
          if (DB::count() == 0) {
            break;
          }
        }
        $volunteer['id'] = substr(md5(microtime()), 0, 6);
      }

      DB::insertUpdate(
        'volunteer',
        array(
          'id' => $volunteer['id'],
          'name' => $volunteer['name'],
          'email' => $volunteer['email'],
        ),
      );
    }

    Route::redirect(self::getPath());
  }
}
