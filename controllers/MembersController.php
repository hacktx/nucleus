<?hh

class MembersController extends BaseController {
  public static function getPath(): string {
    return '/members';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())->setUserRoles(
      array(UserRole::Superuser, UserRole::Organizer),
    );
  }

  public static function get(): :xhp {
    $page = isset($_GET["page"]) ? (int) $_GET["page"] : 0;
    DB::query("SELECT * FROM users");
    $max_page = (int) (DB::count() / 25);

    return
      <div class="members-wrapper" role="tabpanel">
        {self::getMembers($page, 25)}
        {self::getPagination($page, $max_page)}
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembers(int $page, int $limit): :table {
    $members = <tbody />;

    $offset = $page * $limit;

    $query = DB::query(
      "SELECT * FROM users ORDER BY created ASC LIMIT %i OFFSET %i",
      $limit,
      $offset,
    );
    foreach ($query as $row) {
      $status = <span />;
      switch ($row['status']) {
        case UserState::Pending:
          $status = <span>Pending<span class="pending circle" /></span>;
          break;
        case UserState::Accepted:
          $status = <span>Accepted<span class="accepted circle" /></span>;
          break;
        case UserState::Waitlisted:
          $status = <span>Waitlisted<span class="waitlisted circle" /></span>;
          break;
        case UserState::Rejected:
          $status = <span>Rejected<span class="rejected circle" /></span>;
          break;
      }

      // Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'].' '.$row['lname']}</td>
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

  private static function getPagination(int $page, int $max): :nav {
    $buttons = Vector {};
    for (
      $i = ($page < 2 ? 0 : $page - 2);
      $i < ($page < 2 ? 5 : $page + 3);
      $i++
    ) {
      if ($i > $max) {
        break;
      }

      $buttons[] =
        <li class={$i == $page ? "active" : ""}>
          <a href={self::getPath()."?page=".$i}>{$i}</a>
        </li>;
    }

    $back =
      <li class={$page == 0 ? "disabled" : ""}>
        <a
          href={self::getPath()."?page=".($page < 5 ? 0 : $page - 5)}
          aria-label="Previous">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>;

    $next =
      <li>
        <a
          href=
            {self::getPath()."?page=".($max - $page > 5 ? $page + 5 : $max)}
          aria-label="Next">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>;

    return
      <nav>
        <ul class="pagination">
          {$back}
          {$buttons}
          {$next}
        </ul>
      </nav>;
  }
}
