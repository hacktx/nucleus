<?hh

class Dashboard {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: index.php');
    }

    # Redirect applicants to application page
    $user = Session::getUser();
    if (!$user->isMember()) {
      header('Location: apply.php');
    }

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'http://www.gravatar.com/avatar/' . $email_hash . '?s=300';

    $badges = <p />;
    if($user->isMember()) {
      $badges->appendChild(<span class="label label-success label-as-badge">Member</span>);
    }
    if($user->isAdmin()) {
      $badges->appendChild(<span class="label label-success label-as-badge">Admin</span>);
    }

    return
      <div class="well">
        <div class="row">
          <div class="col-md-3">
            <div class="thumbnail">
              <img src={$gravatar_url} alt="..." />
            </div>
          </div>
          <div class="col-md-9">
            <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
            <p>{$user->getEmail()}</p>
            {$badges}
          </div>
        </div>
      </div>;
  }
}
