<?hh //decl

class EventCheckinController {
  public static function get(): :xhp {
    $event_id = (int)$_SESSION['route_params']['id'];
    $event = Event::genByID($event_id);
    if(!$event) {
      Flash::set('error', 'Invalid Event ID');
      Route::redirect('/events/admin');
    }
    return
      <div class="col-md-5 col-md-offset-3">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$event->getName() . ' check-in'}</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={'/events/' . $event_id}>
              <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" placeholder="Email" />
              </div>
              <button type="submit" class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    $event_id = (int)$_SESSION['route_params']['id'];
    # Make sure all required fields were filled out
    if(!isset($_POST['email'])) {
      Flash::set('error', 'Email is required');
      Route::redirect('/events/' . $event_id);
    }

    $user = User::genByEmail($_POST['email']);

    if(!$user) {
      Flash::set('error', 'User email is invalid');
      Route::redirect('/events/' . $event_id);
    }

    Attendance::create($user->getID(), $event_id);

    Flash::set('success', 'Added event attendance');
    Route::redirect('/events/' . $event_id);
  }
}
