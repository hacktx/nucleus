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
    $settings = Map {
      'applications_open' => Settings::get('applications_open'),
    };

    return
      <x:js-scope>
        <nucleus:settings settings={$settings} />
      </x:js-scope>;
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
