<?hh

class MembersController {
  public static function get(): :xhp {
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
      $roles = Roles::getRoles((int)$row['id']);
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
        if (!in_array(Roles::Admin, $roles)) {
          $buttons->appendChild(
            <button name="admin" class="btn btn-primary" value={$row['id']} type="submit">
              Make admin
            </button>
          );
        }
        if (!in_array(Roles::Reviewer, $roles)) {
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
    # Update the proper field
    if(isset($_POST['delete'])) {
      User::deleteByID((int)$_POST['delete']);
    } elseif (isset($_POST['pledge'])) {
      User::updateStatusByID(User::Pledge, (int)$_POST['pledge']);
    } elseif (isset($_POST['member'])) {
      User::updateStatusByID(User::Member, (int)$_POST['member']);
    } elseif (isset($_POST['admin'])) {
      Roles::insert(Roles::Admin, (int)$_POST['admin']);
    } elseif (isset($_POST['makeReviewer'])) {
      Roles::insert(Roles::Reviewer, (int)$_POST['makeReviewer']);
    } elseif (isset($_POST['removeReviewer'])) {
      Roles::delete(Roles::Reviewer, (int)$_POST['removeReviewer']);
    }

    Route::redirect('/members');
  }
}
