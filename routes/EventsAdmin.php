<?hh

class EventsAdmin {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: /login');
    }

    # User must be an admin to modify events
    $user = Session::getUser();
    if(!$user->isAdmin()) {
      return
        <h1 class="sorry">You do not have access to view this page</h1>;
    }

    # Generate a table of all future events
    $upcoming_events =
      <table class="table">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Location</th>
          <th>DateTime</th>
          <th>Delete</th>
        </tr>
      </table>;

    $query = DB::query("SELECT * FROM events WHERE datetime >= CURDATE()");
    foreach($query as $row) {
      $upcoming_events->appendChild(
        <tr>
          <td>{$row['id']}</td>
          <td>{$row['name']}</td>
          <td>{$row['location']}</td>
          <td>{$row['datetime']}</td>
          <td>
            <form method="post" action="/events/admin">
              <button name="delete" class="btn btn-danger" value={$row['id']} type="submit">
                Delete
              </button>
            </form>
          </td>
        </tr>
      );
    }

    return
      <div class="col-md-12">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Create New Event</h1>
          </div>
          <div class="panel-body">
            <form method="post" action="/events/admin">
              <div class="form-group">
                <label>Name</label>
                <input type="text" class="form-control" name="name" />
              </div>
              <div class="form-group">
                <label>Location</label>
                <input type="text" class="form-control" name="location" />
              </div>
              <div class="form-group">
                <label>Date</label>
                <input type="date" class="form-control" name="date" />
              </div>
              <div class="form-group">
                <label>Time</label>
                <input type="time" class="form-control" name="time" />
              </div>
              <button type="submit" name="create" value="1" class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Upcoming Events</h1>
          </div>
          <div class="panel-body">
            {$upcoming_events}
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    if(!Session::isActive()) {
      header('Location: /login');
    }

    # User has to be an admin to modify events
    $user = Session::getUser();
    if(!$user->isAdmin()) {
      header('Location: /events/admin');
    }

    # We're deleting an event
    if(isset($_POST['delete'])) {
      DB::delete('events', 'id=%s', $_POST['delete']);
      header('Location: /events/admin');
    }

    # All fields must be present
    if(!isset($_POST['name']) ||
       !isset($_POST['location']) ||
       !isset($_POST['date']) ||
       !isset($_POST['time'])) {
      header('Location: /events/admin');
    }

    Event::create($_POST['name'], $_POST['location'], $_POST['date'], $_POST['time']);
    header('Location: /events/admin');
  }
}
