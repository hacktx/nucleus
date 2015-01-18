<?hh

class Members {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: /login');
    }
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
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", $status);
    $members =
      <table class="table table-striped">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </table>;
    foreach($query as $row) {
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
      } elseif ($row['admin'] == false) {
        $buttons->appendChild(
          <button name="admin" class="btn btn-primary" value={$row['id']} type="submit">
            Make admin
          </button>
        );
      }

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
    if(!$user->isAdmin) {
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
        'is_admin' => true
      ), "id=%s", $_POST['admin']);
    }

    header('Location: /members');
  }
}
