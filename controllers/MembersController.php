<?hh

class MembersController extends BaseController {
  public static function getPath(): string {
    return '/members';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserState(array(UserState::Member))
      ->setUserRoles(array(UserRole::Admin));
  }

  public static function get(): :xhp {
    return
      <div class="well" role="tabpanel">
        <ul class="nav nav-tabs nav-justified" role="tablist">
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
        <br />
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="members">{self::getMembersByStatus(2)}</div>
          <div role="tabpanel" class="tab-pane" id="pledges">{self::getMembersByStatus(1)}</div>
          <div role="tabpanel" class="tab-pane" id="applicants">{self::getMembersByStatus(0)}</div>
        </div>
        {self::getModal()}
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembersByStatus(int $status): :table {
    $members = <tbody />;

    # Loop through all users with the specified status
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", $status);
    foreach($query as $row) {
      $roles = Roles::getRoles((int)$row['id']);
      # Generate the action buttons based off the user's role and status
      $buttons = <form class="btn-toolbar" method="post" action="/members" />;
      if($row['member_status'] == User::Applicant) {
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
      } elseif ($row['member_status'] == User::Pledge) {
        $buttons->appendChild(
          <button name="member" class="btn btn-primary" value={$row['id']} type="submit">
            Promote to member
          </button>
        );
      } else {
        $buttons->appendChild(
          <button
            type="button"
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#editRoles"
            data-id={$row['id']}
            data-name={$row['fname'] . ' ' . $row['lname']}
            data-roles={json_encode($roles)}>
            Edit Roles
          </button>
        );
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

    return
      <table class="table table-bordered table-striped sortable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th data-defaultsort="disabled">Actions</th>
          </tr>
        </thead>
        {$members}
      </table>;

  }


  private static function getModal(): :xhp {
    $form = <form action="/members" method="post" />;
    $refl = new ReflectionClass('Roles');
    foreach($refl->getConstants() as $role) {
      $form->appendChild(
        <div class="checkbox">
          <label>
            <input type="checkbox" id={(string)$role} name={(string)$role} /> {ucwords($role)}
          </label>
        </div>
      );
    }
    $form->appendChild(
      <input type="hidden" name="id" />
    );
    return
      <div class="modal fade" id="editRoles" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h3 class="modal-title" id="editRolesName" />
            </div>
            <div class="modal-body">
              {$form}
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" id="submit">Save</button>
            </div>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    # Update the proper field
    if(isset($_POST['delete'])) {
      User::deleteByID((int)$_POST['delete']);
    } elseif (isset($_POST['pledge'])) {
      User::updateStatusByID(User::Pledge, (int)$_POST['pledge']);
    } elseif (isset($_POST['member'])) {
      User::updateStatusByID(User::Member, (int)$_POST['member']);
    } else {
      $refl = new ReflectionClass('Roles');
      foreach($refl->getConstants() as $role) {
        $user_roles = Roles::getRoles((int)$_POST['id']);
        if(isset($_POST[$role])) {
          if(!in_array($role, $user_roles)) {
            Roles::insert((string)$role, (int)$_POST['id']);
          }
        } else {
          if(in_array($role, $user_roles)) {
            Roles::delete((string)$role, (int)$_POST['id']);
          }
        }
      }
    }

    Route::redirect('/members');
  }
}
