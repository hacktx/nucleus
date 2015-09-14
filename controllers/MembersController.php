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

    $search = isset($_GET["search"]) ? (string) $_GET["search"] : null;

    $page = isset($_GET["page"]) ? (int) $_GET["page"] : 0;

    $limit = 25;
    $offset = $page * $limit;

    if ($filter !== null) {
      DB::query("SELECT * FROM users WHERE status=%i", $filter);
    } else if ($search !== null) {
      DB::query(
        "SELECT * FROM users WHERE %s in (fname, lname, email)",
        $search,
      );
    } else {
      DB::query("SELECT * FROM users");
    }

    $max_page = (int) (DB::count() / 25);

    if ($filter !== null) {
      $members =
        DB::query(
          "SELECT * FROM users WHERE status=%i ORDER BY created ASC LIMIT %i OFFSET %i",
          $filter,
          $limit,
          $offset,
        );
    } else if ($search !== null) {
      $members =
        DB::query(
          "SELECT * FROM users WHERE %s in (fname, lname, email) ORDER BY created ASC LIMIT %i OFFSET %i",
          $search,
          $limit,
          $offset,
        );
    } else {
      $members = DB::query(
        "SELECT * FROM users ORDER BY created ASC LIMIT %i OFFSET %i",
        $limit,
        $offset,
      );
    }

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
      <div class="memberscontroller-wrapper">
        <div class="row text-right">
          <input
            id="member-search"
            class="search-bar"
            type="search"
            placeholder="Search"
          />
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="list-group">
              {$filters}
            </div>
            {$clear_filter}
          </div>
          <div class="members-wrapper col-md-10" role="tabpanel">
            {self::getMembers($members)}
            <nucleus:pagination
              path={self::getPath()}
              filter={$filter}
              page={$page}
              max={$max_page}
            />
          </div>
        </div>
        <script src="/js/members.js"></script>
        <script src="/js/search.js"></script>
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

  private static function getMembers(array $query): :table {
    $members = <tbody />;

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
}
