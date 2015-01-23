<?hh //decl

class FeedbackSingleController {
  public static function get(): :xhp {
    $user_id = (int)$_SESSION['route_params']['id'];
    $user = User::genByID($user_id);

    $feedback = Feedback::gen($user_id, Session::getUser()->getID());

    return
      <div class="col-md-8 col-md-offset-2">
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1>{$user->getFirstName() . ' ' . $user->getLastName()}</h1>
          </div>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h1 class="panel-title">Review</h1>
          </div>
          <div class="panel-body">
            <form method="post" action={'/feedback/' . $user_id}>
              <div class="form-group">
                <label for="review" class="control-label">Comments</label>
                <textarea class="form-control" rows="5" id="feedback" name="feedback">
                  {$feedback->getComments()}
                </textarea>
              </div>
              <button type="submit" name="id" value={$user_id} class="btn btn-default">Submit</button>
            </form>
          </div>
        </div>
      </div>;
  }

  public static function post(): void {
    # Upsert the review
    Feedback::upsert(
      $_POST['feedback'],
      (int)$_POST['id'],
      Session::getUser()->getID()
    );

    Flash::set('success', 'Feedback submitted!');
    Route::redirect('/feedback/' . $_POST['id']);
  }
}
