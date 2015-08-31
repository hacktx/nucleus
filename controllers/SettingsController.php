<?hh

class SettingsController extends BaseController {
  public static function getPath(): string {
    return '/settings';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserRoles(array(UserRole::Superuser));
  }

  public static function get(): :xhp {
    $applications_open = Settings::get('applications_open');
    return
      <div class="col-md-6 col-md-offset-3">
        <form class="form" action="/settings" method="post">
          <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                  <div class="checkbox">
                    <label>
                      <input type="checkbox" name="applications_disabled" checked={!$applications_open}/> Disable Applications
                    </label>
                  </div>
                </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary pull-right">Save</button>
        </form>
      </div>;
  }

  public static function post(): void {
    if(isset($_POST['applications_disabled'])) {
      Settings::set('applications_open', false);
    } else {
      Settings::set('applications_open', true);
    }

    Route::redirect('/settings');
  }
}
