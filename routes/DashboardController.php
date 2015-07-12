<?hh

class DashboardController {
  public static function get(): :xhp {
    $user = Session::getUser();

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'https://secure.gravatar.com/avatar/' . $email_hash . '?s=300';

    $badges = <p />;
    $badges->appendChild(
      <span class="label label-warning">{ucwords($user->getStatus())}</span>
    );

    $applicant_info = null;
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

    $events = null;
    if(!$user->isDisabled()) {
      $events = Event::genAllFuture();
      if(!empty($events)) {
        $events =
          <div class="panel panel-default">
            <div class="panel-heading">
              <h1 class="panel-title">Upcoming Events</h1>
            </div>
            <div class="panel-body">
              <nucleus:event-list events={$events} />
            </div>
          </div>;
      }
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
