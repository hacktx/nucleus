<?hh

class Review {
  public static function get(): :xhp {
    if(!Session::isActive()) {
      header('Location: /login');
    }
    $user = Session::getUser();
    if(!$user->isAdmin()) {
      return
        <h1 class="sorry">You do not have access to view this page</h1>;
    }

    parse_str($_SERVER['QUERY_STRING'], $query_params);

    if(isset($query_params['app_id'])) {
      return self::singleApplication($query_params['app_id']);
    } else {
      return self::applicationList();
    }
  }

  private static function applicationList(): :xhp {
    $table = <table class="table table-bordered table-striped" />;
    $table->appendChild(
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
      </tr>
    );

    $query = DB::query("SELECT * FROM applications");

    foreach($query as $row) {
      $user = User::genByID($row['user_id']);
      $table->appendChild(
        <tr>
          <td>{$row['id']}</td>
          <td>{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td>{$user->getEmail()}</td>
        </tr>
      );
    }

    return
      <div class="well">
        {$table}
      </div>;
  }

  private static function singleApplication(string $app_id): :xhp {
    $application = Application::genByID($app_id);
    $user = User::genByID($application->getUserID());

    return
      <div class="panel panel-default col-md-8 col-md-offset-2">
        <div class="panel-heading">
          <h3 class="panel-title">{$user->getFirstName() . ' ' . $user->getLastName()}</h3>
        </div>
        <div class="panel-body">
          Panel content
        </div>
      </div>;
  }
}
