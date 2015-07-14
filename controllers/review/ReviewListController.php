<?hh //decl

class ReviewListController extends BaseController {
  public static function getPath(): string {
    return '/review';
  }

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig())
      ->setUserStatus(array(User::Member))
      ->setUserRoles(array(Roles::Reviewer, Roles::Admin));
  }

  public static function get(): :xhp {
    $table = <table class="table table-bordered table-striped sortable" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>{'# Reviews'}</th>
          <th>Avg Rating</th>
          <th data-defaultsort="disabled">Review</th>
        </tr>
      </thead>
    );

    # Loop through all the applications that are submitted
    $query = DB::query("SELECT * FROM applications WHERE status=2");
    $table_body = <tbody class="list" />;
    foreach($query as $row) {
      # Get the user the application belongs to
      $user = User::genByID($row['user_id']);

      # Skip the user if they're no longer an applicant
      if(!$user->isApplicant()) {
        continue;
      }

      $query = DB::queryFirstRow("SELECT COUNT(*) FROM reviews WHERE application_id=%s", $row['id']);
      $count = $query['COUNT(*)'];

      # Get the average rating
      $query = DB::queryFirstRow("SELECT AVG(rating) FROM reviews WHERE application_id=%s", $row['id']);
      $avg_rating = number_format($query['AVG(rating)'], 2);

      # Get the current user's review
      DB::query("SELECT * FROM reviews WHERE user_id=%s AND application_id=%s", Session::getUser()->getID(), $row['id']);

      # Append the applicant to the table as a new row
      $table_body->appendChild(
        <tr class={DB::count() != 0 ? "success" : ""} id={$row['id']}>
          <td>{$row['id']}</td>
          <td class="name">{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td class="email">{$user->getEmail()}</td>
          <td class="text-center">{$count}</td>
          <td class="text-center">{$avg_rating}</td>
          <td class="text-center"><a href={'/review/' . $row['id']} class="btn btn-primary">Review</a></td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <x:frag>
        <div id="applications" class="well">
          <input class="search form-control" placeholder="Search" />
          <br/>
          {$table}
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
        <script src="/js/review.js"></script>
        <script src="/js/moment.min.js"></script>
        <script src="/js/bootstrap-sortable.js"></script>
      </x:frag>;
  }
}
