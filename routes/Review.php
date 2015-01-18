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
        <th>Review</th>
      </tr>
    );

    $query = DB::query("SELECT * FROM applications WHERE status=2");

    foreach($query as $row) {
      $user = User::genByID($row['user_id']);
      DB::query("SELECT * FROM reviews WHERE user_id=%s AND application_id=%s", $row['user_id'], $row['id']);
      $table->appendChild(
        <tr class={DB::count() != 0 ? "success" : ""}>
          <td>{$row['id']}</td>
          <td>{$user->getFirstName() . ' ' . $user->getLastName()}</td>
          <td>{$user->getEmail()}</td>
          <td><a href={'/review?app_id=' . $row['id']} class="btn btn-primary">Review</a></td>
        </tr>
      );
    }

    return
      <div class="well">
        {$table}
      </div>;
  }

  private static function singleApplication(string $app_id): :xhp {
    $application = Application::genByID((int)$app_id);
    $user = User::genByID($application->getUserID());
    $review = AppReview::genByUserAndApp($user, $application);

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
          </div>
          <table class="table">
            <tr>
              <th>Gender</th>
              <th>Year</th>
              <th>Email</th>
            </tr>
            <tr>
              <td>{$application->getGender()}</td>
              <td>{$application->getYear()}</td>
              <td>
                <a href={'mailto:' . $user->getEmail()} target="_blank">
                  {$user->getEmail()}
                </a>
              </td>
            </tr>
          </table>
          <div class="panel-body">
            <h4>Why do you want to rush Lambda Alpha Nu?</h4>
            <p>{$application->getQ1()}</p>
            <hr/>
            <h4>Talk about yourself in a couple of sentences.</h4>
            <p>{$application->getQ2()}</p>
            <hr/>
            <h4>What is your major and why did you choose it?</h4>
            <p>{$application->getQ3()}</p>
            <hr/>
            <h4>What do you do in your spare time?</h4>
            <p>{$application->getQ4()}</p>
            <hr/>
            <h4>Talk about a current event in technology and why it interests you.</h4>
            <p>{$application->getQ5()}</p>
            <hr/>
            <h4>Impress us</h4>
            <p>{$application->getQ6()}</p>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Review</h1>
          </div>
          <div class="panel-body">
            <form method="post" action="/review">
              <div class="form-group">
                <label for="review" class="control-label">Comments</label>
                <textarea class="form-control" rows="3" id="review" name="review">
                  {$review->getComments()}
                </textarea>
              </div>
              <div class="form-group">
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="1" checked={$review->getRating() == 1} /> Strong No
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="2" checked={$review->getRating() == 2} /> Weak No
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="3" checked={$review->getRating() == 3} /> Neutral
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="4" checked={$review->getRating() == 4} /> Weak Yes
                  </label>
                </div>
                <div class="radio">
                  <label>
                    <input type="radio" name="weight" value="5" checked={$review->getRating() == 5} /> Strong Yes
                  </label>
                </div>
              </div>
              <button type="submit" name="id" value={$application->getID()} class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    AppReview::upsert(
      $_POST['review'],
      (int)$_POST['weight'],
      Session::getUser(),
      Application::genByID((int)$_POST['id'])
    );

    header('Location: /review?app_id=' . $_POST['id']);
  }
}
