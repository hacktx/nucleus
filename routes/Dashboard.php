<?hh

class Dashboard {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: /');
    }
    $user = Session::getUser();

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'http://www.gravatar.com/avatar/' . $email_hash . '?s=300';

    $badges = <p />;
    $applicant_info = null;
    if($user->isApplicant()) {
      $badges->appendChild(<span class="label label-warning">Applicant</span>);
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
          <div class="col-md-12">
            {$status}
          </div>
        </div>;
    }
    if($user->isPledge()) {
      $badges->appendChild(<span class="label label-info">Pledge</span>);
    }
    if($user->isMember()) {
      $badges->appendChild(<span class="label label-success">Member</span>);
    }
    if($user->isAdmin()) {
      $badges->appendChild(<span class="label label-success">Admin</span>);
    }

    return
      <div class="panel panel-default">
        <div class="panel-body">
          <div class="col-md-3">
            <img src={$gravatar_url} class="img-thumbnail" />
          </div>
          <div class="col-md-9">
            <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
            <p>{$user->getEmail()}</p>
            {$badges}
          </div>
        </div>
        {$applicant_info}
      </div>;
  }
}
