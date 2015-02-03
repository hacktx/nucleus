<?hh

class DashboardController {
  public static function get(): :xhp {
    $user = Session::getUser();

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'http://www.gravatar.com/avatar/' . $email_hash . '?s=300';

    $badges = <p />;
    $badges->appendChild(
      <span class="label label-warning">{ucwords($user->getStatus())}</span>
    );

    $applicant_info = null;
    $events = null;
    if($user->isApplicant()) {
      $application = Application::genByUser($user);
      if(!$application->isStarted() && !$application->isSubmitted()) {
        $status = <a href="/apply" class="btn btn-primary btn-lg wide">Start Application</a>;
      } elseif($application->isStarted() && !$application->isSubmitted()) {
        $status = <a href="/apply" class="btn btn-primary btn-lg wide">Finish Application</a>;
      } else {
        $status = <h3>Application Status: <span class="label label-info">Under review</span></h3>;
      }
      $applicant_info =
        <div class="panel-body">
          {$status}
        </div>;
    }

    $query = DB::query("SELECT * FROM events WHERE datetime >= CURDATE()");
    $event_list =
      <table class="table">
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
        </tr>
      </table>;
    foreach($query as $row) {
      $timestamp = strtotime($row['datetime']);
      $event_list->appendChild(
        <tr>
          <td>{$row['name']}</td>
          <td>{$row['location']}</td>
          <td>{date('n/j/Y \@ g:i A', $timestamp)}</td>
        </tr>
      );
    }
    if(!empty($query)) {
      $events =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Upcoming Events</h1>
          </div>
          <div class="panel-body">
            {$event_list}
          </div>
        </div>;
    }

    $roles = $user->getRoles();
    foreach($roles as $role) {
      $badges->appendChild(<span class="label label-success">{ucwords($role)}</span>);
    }

    return
      <x:frag>
        <div class="panel panel-default">
          <div class="panel-body">
            <div class="col-md-3">
              <div class="thumbnail">
                <img src={$gravatar_url} class="img-thumbnail" />
                <div class="caption">
                  <p><a href="https://en.gravatar.com/emails/" class="wide btn btn-primary" role="button">Change on Gravatar</a></p>
                </div>
              </div>
            </div>
            <div class="col-md-9">
              <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
              <p>{$user->getEmail()}</p>
              {$badges}
            </div>
          </div>
          {$applicant_info}
        </div>
        {$events}
      </x:frag>;
  }
}
