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
    $filter =
      isset($_GET["filter"]) ? UserState::assert($_GET["filter"]) : null;

    $page = isset($_GET["page"]) ? (int) $_GET["page"] : 0;

    if ($filter !== null) {
      DB::query("SELECT * FROM users WHERE status=%i", $filter);
    } else {
      DB::query("SELECT * FROM users");
    }
    $max_page = (int) (DB::count() / 25);

    return
      <div class="row">
        <div class="col-md-2">
          <div class="list-group">
            <a
              href={self::getPath()."?filter=".UserState::Pending}
              class=
                {"list-group-item ".
                ($filter === UserState::Pending ? "active" : "")}>
              Pending
            </a>
            <a
              href={self::getPath()."?filter=".UserState::Accepted}
              class=
                {"list-group-item ".
                ($filter === UserState::Accepted ? "active" : "")}>
              Accepted
            </a>
            <a
              href={self::getPath()."?filter=".UserState::Waitlisted}
              class=
                {"list-group-item ".
                ($filter === UserState::Waitlisted ? "active" : "")}>
              Waitlisted
            </a>
            <a
              href={self::getPath()."?filter=".UserState::Rejected}
              class=
                {"list-group-item ".
                ($filter === UserState::Rejected ? "active" : "")}>
              Rejected
            </a>
          </div>
        </div>
        <div class="members-wrapper col-md-10" role="tabpanel">
          {self::getMembers($page, 25, $filter)}
          {self::getPagination($page, $max_page, $filter)}
        </div>
        <script src="/js/members.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </div>;
  }

  private static function getMembers(
    int $page,
    int $limit,
    ?UserState $filter,
  ): :table {
    $members = <tbody />;

    $offset = $page * $limit;

    $query = [];
    if ($filter !== null) {
      $query =
        DB::query(
          "SELECT * FROM users WHERE status=%i ORDER BY created ASC LIMIT %i OFFSET %i",
          $filter,
          $limit,
          $offset,
        );
    } else {
      $query = DB::query(
        "SELECT * FROM users ORDER BY created ASC LIMIT %i OFFSET %i",
        $limit,
        $offset,
      );
    }

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

  private static function getPagination(int $page, int $max, ?UserState $filter): :nav {
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
          <a href={self::getLink($i, $filter)}>{$i}</a>
        </li>;
    }

    $beginning =
      <li class={$page < 3 ? "disabled" : ""}>
        <a href={self::getLink(0, $filter)} aria-label="Beginning">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>;

    $back =
      <li class={$page < 3 ? "disabled" : ""}>
        <a
          href={self::getLink($page < 5 ? 0 : $page - 5, $filter)}
          aria-label="Previous">
          <span aria-hidden="true">&lsaquo;</span>
        </a>
      </li>;

    $next =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a
          href={self::getLink($max - $page > 5 ? $page + 5 : $max, $filter)}
          aria-label="Next">
          <span aria-hidden="true">&rsaquo;</span>
        </a>
      </li>;

    $end =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a href={self::getLink($max, $filter)} aria-label="End">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>;

    return
      <nav>
        <ul class="pagination">
          {$beginning}
          {$back}
          {$buttons}
          {$next}
          {$end}
        </ul>
      </nav>;
  }

  private static function getLink(int $page, ?UserState $filter): string {
    $url = self::getPath()."?page=".$page;
    if ($filter !== null) {
      $url = $url."&filter=".$filter;
    }

    return $url;
  }
}
