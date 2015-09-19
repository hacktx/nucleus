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

    $uri_builder = new URIBuilder(self::getPath());

    $where = new WhereClause("and");
    if ($filter !== null) {
      $uri_builder->setParam('filter', $filter);
      $where->add("status=%i", $filter);
    } else if ($search !== null) {
      $uri_builder->setParam('search', $search);
      $where->add(
        "MATCH(fname, lname, email) AGAINST (%s IN BOOLEAN MODE)",
        preg_replace("/(\w+)/", "+$1*", $search),
      );
    }

    DB::query("SELECT * FROM users WHERE %l", $where);
    $max_page = (int) (DB::count() / 25);

    $members = DB::query(
      "SELECT * FROM users WHERE %l ORDER BY created ASC LIMIT %i OFFSET %i",
      $where,
      $limit,
      $offset,
    );

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

    $progress_bar_types = Map {
      "Accepted" => "",
      "Confirmed" => "progress-bar-success",
      "Waitlisted" => "progress-bar-warning",
      "Rejected" => "progress-bar-danger",
    };

    DB::query("SELECT * FROM users");
    $total_users = DB::count();

    $progress_bars = Vector {};
    foreach ($progress_bar_types as $state => $class) {
      DB::query(
        "SELECT * FROM users WHERE status=%s",
        UserState::getValues()[$state],
      );
      $percent = DB::count() / $total_users * 100;
      $progress_bars[] =
        <div class={"progress-bar ".$class} style={"width: ".$percent."%"}>
          <span>{(int) $percent."% ".$state}</span>
        </div>;
    }

    return
      <div class="memberscontroller-wrapper">
        <div class="row text-right">
          <div class="col-md-10 col-md-offset-2">
            <input
              id="member-search"
              class="search-bar"
              type="search"
              value={$search}
              placeholder="Search"
            />
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="progress">
              {$progress_bars}
            </div>
          </div>
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
              uri-builder={$uri_builder}
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
