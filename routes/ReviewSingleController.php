<?hh //decl

class ReviewSingleController {
  public static function get(): :xhp {
    $app_id = (int)$_SESSION['route_params']['id'];

    $application = Application::genByID((int)$app_id);
    $user = User::genByID($application->getUserID());
    $review = AppReview::genByUserAndApp(Session::getUser(), $application);

    # Admins get special actions like delete and promote
    $admin_controls = null;
    if(Session::getUser()->isAdmin()) {
      $admin_controls =
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Admin Actions</h1>
          </div>
          <div class="panel-body">
            <form class="btn-toolbar" method="post" action="/members">
              <button name="pledge" class="btn btn-primary" value={$user->getID()} type="submit">
                Promote to Pledge
              </button>
              <button name="delete" class="btn btn-danger" value={$user->getID()} type="submit">
                Delete this application
              </button>
            </form>
          </div>
        </div>;
    }

    $email_hash = md5(strtolower(trim($user->getEmail())));
    $gravatar_url = 'https://secure.gravatar.com/avatar/' . $email_hash . '?s=200';

    $query = DB::queryFirstRow("SELECT AVG(rating) FROM reviews WHERE application_id=%s", $app_id);
    $avg_rating = number_format($query['AVG(rating)'], 2);

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
          </div>
          <div class="panel-body">
            <p class="text-center">
              <img src={$gravatar_url} class="img-thumbnail" />
            </p>
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
        {$admin_controls}
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Review</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={'/review/' . $user->getID()}>
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
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Average Rating</h1>
          </div>
          <div class="panel-body">
            <h1 class="text-center">{$avg_rating . ' / 5.00'}</h1>
          </div>
        </div>
        {self::getReviews($application)}
      </div>;
  }

  public static function post(): void {
    # Upsert the review
    AppReview::upsert(
      $_POST['review'],
      (int)$_POST['weight'],
      Session::getUser(),
      Application::genByID((int)$_POST['id'])
    );

    Route::redirect('/review#' . $_POST['id']);
  }

  private static function getReviews(Application $application): ?:xhp {

    # Loop through the reviews
    $query = DB::query("SELECT * FROM reviews WHERE application_id=%s", $application->getID());
    $reviews = <ul class="list-group" />;
    foreach($query as $row) {
      $user = User::genByID($row['user_id']);
      $reviews->appendChild(
        <li class="list-group-item">
          <h4>{$user->getFirstName() . ' ' . $user->getLastName()}</h4>
          <p>{$row['comments']}</p>
        </li>
      );
    }

    # Loop through member feedback
    $query = DB::query("SELECT * FROM feedback WHERE user_id=%s", $application->getUserID());
    $feedback = <ul class="list-group" />;
    foreach($query as $row) {
      # Skip empty feedback
      if($row['comments'] === '') {
        continue;
      }
      $user = User::genByID($row['reviewer_id']);
      $feedback->appendChild(
        <li class="list-group-item">
          <h4>{$user->getFirstName() . ' ' . $user->getLastName()}</h4>
          <p>{$row['comments']}</p>
        </li>
      );
    }

    $attendances = Attendance::genAllForUser($application->getUserID());
    $events = <ul class="list-group" />;
    foreach($attendances as $attendance) {
      $event = Event::genByID($attendance->getEventID());
      $events->appendChild(
        <li class="list-group-item">
          <h4>{$event->getName()}</h4>
        </li>
      );
    }

    return
      <div class="panel panel-default" role="tabpanel">
        <div class="panel-heading">
          <ul class="nav nav-pills" role="tablist">
            <li role="presentation" class="active">
              <a href="#reviews" aria-controls="home" role="tab" data-toggle="tab">Reviews</a>
            </li>
            <li role="presentation">
              <a href="#feedback" aria-controls="profile" role="tab" data-toggle="tab">Member Feedback</a>
            </li>
            <li role="presentation">
              <a href="#events" aria-controls="profile" role="tab" data-toggle="tab">Events Attended</a>
            </li>
          </ul>
        </div>
        <div class="tab-content">
          <br/>
          <div role="tabpanel" class="tab-pane active" id="reviews">{$reviews}</div>
          <div role="tabpanel" class="tab-pane" id="feedback">{$feedback}</div>
          <div role="tabpanel" class="tab-pane" id="events">{$events}</div>
        </div>
      </div>;
  }
}
