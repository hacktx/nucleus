<?hh

class NotifyController extends BaseController {
  public static function getPath(): string {
    return '/notify';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserState(array(UserState::Member))
      ->setUserRoles(array(UserRole::Admin, UserRole::Officer));
  }

  public static function get(): :xhp {
    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Send Notification</h1>
          </div>
          <div class="panel-body">
            <form method="post" action="/notify">
              <div class="form-group">
                <label>Mailing List</label>
                <input type="text" class="form-control" name="email" />
              </div>
              <div class="form-group">
                <label>Subject</label>
                <input type="text" class="form-control" name="subject" />
              </div>
              <div class="form-group">
                <label>Body</label>
                <textarea class="form-control" rows={3} name="body"></textarea>
              </div>
              <button type="submit" class="btn btn-default">Send</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    if(!isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['body'])) {
      Flash::set(Flash::ERROR, 'All fields must be filled out');
      Route::redirect('/notify');
    }

    Email::send($_POST['email'], $_POST['subject'], $_POST['body']);
    Flash::set(Flash::SUCCESS, 'Your email was sent successfully');
    Route::redirect('/notify');
  }
}
