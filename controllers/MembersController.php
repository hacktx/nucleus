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
      <div class="members-wrapper" role="tabpanel">
        {self::getMembers()}
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembers(): :table {
    $members = <tbody />;

    $query = DB::query("SELECT * FROM users");
    foreach($query as $row) {
      $status = <span/>;
      switch($row['status']) {
        case UserState::Pending:
          $status = <span>Pending<span class="pending circle"/></span>;
          break;
        case UserState::Accepted:
          $status = <span>Accepted<span class="accepted circle"/></span>;
          break;
        case UserState::Waitlisted:
          $status = <span>Waitlisted<span class="waitlisted circle"/></span>;
          break;
        case UserState::Rejected:
          $status = <span>Rejected<span class="rejected circle"/></span>;
          break;
      }
      
      // Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'] . ' ' . $row['lname']}</td>
          <td>{$row['school']}</td>
          <td>{$row['major']}</td>
          <td>{$status}</td>
        </tr>
      );
    }

    return
      <table class="table table-hover sortable">
        <thead>
          <tr>
            <th class="text-center">Name</th>
            <th class="text-center">School</th>
            <th class="text-center">Major</th>
            <th class="text-center">Status</th>
          </tr>
        </thead>
        {$members}
      </table>;

  }
}
