<?hh //decl

class FeedbackListController {
  public static function get(): :xhp {
    $table = <table class="table table-bordered table-striped" />;
    $table->appendChild(
      <thead>
        <tr>
          <th>Name</th>
          <th>Review</th>
        </tr>
      </thead>
    );

    # Loop through all the applications that are submitted
    $query = DB::query("SELECT * FROM users WHERE member_status=%s", User::Applicant);
    $table_body = <tbody class="list" />;
    foreach($query as $row) {
      # Get the user the application belongs to
      $user = User::genByID($row['id']);

      # Get the current user's review
      DB::query("SELECT * FROM feedback WHERE reviewer_id=%s AND user_id=%s", Session::getUser()->getID(), $row['id']);

      # Append the applicant to the table as a new row
      $table_body->appendChild(
        <tr class={DB::count() != 0 ? "success" : ""}>
          <td class="name">{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td><a href={'/feedback/' . $row['id']} class="btn btn-primary">Review</a></td>
        </tr>
      );
    }

    $table->appendChild($table_body);

    return
      <x:frag>
        <div id="feedback" class="well">
          <input class="search form-control" placeholder="Search" />
          <br/>
          {$table}
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js"></script>
        <script src="/js/feedback.js"></script>
      </x:frag>;
  }
}
