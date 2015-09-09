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

    $clear_filter = null;
    if ($filter !== null) {
      $clear_filter =
        <div class="list-group">
          <a class="list-group-item" href={self::getPath()}>Clear Filters</a>
        </div>;
    }

    $filters = Vector {};
    foreach (UserState::getValues() as $name => $value) {
      $filters[] =
        <a
          href={self::getPath()."?filter=".$value}
          class={"list-group-item ".($filter === $value ? "active" : "")}>
          {$name}
        </a>;
    }

    return
      <div class="row">
        <div class="col-md-2">
          <div class="list-group">
            {$filters}
          </div>
          {$clear_filter}
        </div>
        <div class="members-wrapper col-md-10" role="tabpanel">
          {self::getMembers($page, 25, $filter)}
          {self::getPagination($page, $max_page, $filter)}
        </div>
        <script src="/js/members.js"></script>
      </div>;
  }

  public static function post(): void {
    if (!isset($_POST['user']) ||
        (!isset($_POST['status']) && !isset($_POST['role']))) {
      http_response_code(400);
      return;
    }

    $user_id = (int) $_POST['user'];

    if ($_POST['status'] !== "") {
      User::updateStatusByID(UserState::assert($_POST['status']), $user_id);
    } else if ($_POST['role'] !== "") {
      $role = UserRole::assert($_POST['role']);
      if (!Roles::getRoles($user_id)->contains($role)) {
        Roles::insert($role, $user_id);
      } else {
        Roles::delete($role, $user_id);
      }
    }
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
      $status =
        <span>
          <span class="text">{UserState::getNames()[$row['status']]}</span>
          <span
            class=
              {strtolower(UserState::getNames()[$row['status']])." circle"}
          />
        </span>;

      $menu_options = Vector {};
      $menu_options[] = <li class="dropdown-header">User States</li>;
      foreach (UserState::getValues() as $name => $value) {
        $menu_options[] =
          <li>
            <a href="#" onclick={self::getJSCall($row['id'], $value, null)}>
              {$name}
            </a>
          </li>;
      }

      $menu_options[] = <li class="dropdown-header">User Roles</li>;
      foreach (UserRole::getValues() as $name => $value) {
        $menu_options[] =
          <li>
            <a href="#" onclick={self::getJSCall($row['id'], null, $value)}>
              {$name}
            </a>
          </li>;
      }

      $menu =
        <div class="btn-group">
          <button
            type="button"
            class="btn btn-default dropdown-toggle"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            Options <span class="caret"></span>
          </button>
          <ul class="dropdown-menu">
            {$menu_options}
          </ul>
        </div>;

      // Append the row to the table
      $members->appendChild(
        <tr>
          <td>{$row['fname'].' '.$row['lname']}</td>
          <td>{$row['school']}</td>
          <td>{$row['major']}</td>
          <td id={$row['id']."status"}>{$status}</td>
          <td>{$menu}</td>
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
            <th class="text-center">Change Status</th>
          </tr>
        </thead>
        {$members}
      </table>;

  }

  private static function getJSCall(
    string $id,
    ?UserState $status,
    ?UserRole $role,
  ): string {
    $data = Map {'user' => $id, 'status' => $status, 'role' => $role};
    return "makeCall('".self::getPath()."', ".json_encode($data).");";
  }

  private static function getPagination(
    int $page,
    int $max,
    ?UserState $filter,
  ): :nav {
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
