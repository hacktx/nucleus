<?hh

class Members {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: /login');
    }

    # Only admins can view this page
    $user = Session::getUser();
    if(!$user->isAdmin()) {
      return
        <h1 class="sorry">You do not have access to view this page</h1>;
    }

    return
      <div class="well" role="tabpanel">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#members" aria-controls="home" role="tab" data-toggle="tab">Members</a>
          </li>
          <li role="presentation">
            <a href="#pledges" aria-controls="profile" role="tab" data-toggle="tab">Pledges</a>
          </li>
          <li role="presentation">
            <a href="#applicants" aria-controls="profile" role="tab" data-toggle="tab">Applicants</a>
          </li>
        </ul>
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="members">{self::getMembersByStatus(2)}</div>
          <div role="tabpanel" class="tab-pane" id="pledges">{self::getMembersByStatus(1)}</div>
          <div role="tabpanel" class="tab-pane" id="applicants">{self::getMembersByStatus(0)}</div>
        </div>
      </div>;
  }

  private static function getMembersByStatus(int $status): :table {
    $members =
      <table class="table table-striped">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </table>;

    # Loop through all users with the specified status
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", $status);
    foreach($query as $row) {
      # Generate the action buttons based off the user's role and status
      $buttons = <form class="btn-toolbar" method="post" action="/members" />;
      if($row['member_status'] == 0) {
        $buttons->appendChild(
          <button name="pledge" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to pledge
          </button>
        );
        $buttons->appendChild(
          <button name="delete" class="btn btn-danger" value={$row['id']} type="submit">
            Delete
          </button>
        );
      } elseif ($row['member_status'] == 1) {
        $buttons->appendChild(
          <button name="member" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to member
          </button>
        );
      } else {
        if ($row['admin'] == false) {
          $buttons->appendChild(
            <button name="admin" class="btn btn-primary" value={$row['id']} type="submit">
              Make admin
            </button>
          );
        }
        if (!$row['reviewer']) {
          $buttons->appendChild(
            <button name="makeReviewer" class="btn btn-primary" value={$row['id']} type="submit">
              Make Reviewer
            </button>
          );
        } else {
          $buttons->appendChild(
            <button name="removeReviewer" class="btn btn-danger" value={$row['id']} type="submit">
              Remove Reviewer
            </button>
          );
        }
      }

      # Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'] . ' ' . $row['lname']}</td>
          <td>{$row['email']}</td>
          <td>{$buttons}</td>
        </tr>
      );
    }

    return $members;
  }

  public static function post(): void {
    if(!Session::isActive()) {
      header('Location: /login');
    }

    # Check auth level
    $user = Session::getUser();
    if(!$user->isAdmin()) {
      header('Location /members');
    }

    # Update the proper field
    if(isset($_POST['delete'])) {
      DB::delete('users', 'id=%s', $_POST['delete']);
    } elseif (isset($_POST['pledge'])) {
      DB::update('users', array(
        'member_status' => 1
      ), "id=%s", $_POST['pledge']);
    } elseif (isset($_POST['member'])) {
      DB::update('users', array(
        'member_status' => 2
      ), "id=%s", $_POST['member']);
    } elseif (isset($_POST['admin'])) {
      DB::update('users', array(
        'admin' => true
      ), "id=%s", $_POST['admin']);
    } elseif (isset($_POST['makeReviewer'])) {
      DB::update('users', array(
        'reviewer' => true
      ), "id=%s", $_POST['makeReviewer']);
    } elseif (isset($_POST['removeReviewer'])) {
       DB::update('users', array(
        'reviewer' => false
      ), "id=%s", $_POST['removeReviewer']);
    }

    header('Location: /members');
  }
}
