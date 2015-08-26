<?hh

class MembersController extends BaseController {
  public static function getPath(): string {
    return '/members';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserRoles(array(UserRole::Superuser, UserRole::Organizer));
  }

  public static function get(): :xhp {
    return
      <div class="well" role="tabpanel">
        <ul class="nav nav-tabs nav-justified" role="tablist">
          <li role="presentation" class="active">
            <a href="#accepted" aria-controls="home" role="tab" data-toggle="tab">Accepted</a>
          </li>
          <li role="presentation">
            <a href="#waitlisted" aria-controls="profile" role="tab" data-toggle="tab">Waitlisted</a>
          </li>
          <li role="presentation">
            <a href="#pending" aria-controls="profile" role="tab" data-toggle="tab">Pending</a>
          </li>
          <li role="presentation">
            <a href="#rejected" aria-controls="profile" role="tab" data-toggle="tab">Rejected</a>
          </li>
        </ul>
        <br />
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="accepted">{self::getMembersByState(UserState::Accepted)}</div>
          <div role="tabpanel" class="tab-pane" id="waitlisted">{self::getMembersByState(UserState::Waitlisted)}</div>
          <div role="tabpanel" class="tab-pane" id="pending">{self::getMembersByState(UserState::Pending)}</div>
          <div role="tabpanel" class="tab-pane" id="rejected">{self::getMembersByState(UserState::Rejected)}</div>
        </div>
        {self::getModal()}
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembersByState(UserState $status): :table {
    $members = <tbody />;

    $query = DB::query("SELECT * FROM users WHERE status=%s", $status);
    foreach($query as $row) {
      # Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'] . ' ' . $row['lname']}</td>
          <td>{$row['email']}</td>
        </tr>
      );
    }

    return
      <table class="table table-bordered table-striped sortable">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
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
}
