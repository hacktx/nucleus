<?hh

class Apply {
  public static function get(): :xhp {
    # You must be authed to view the application
    if(!Session::isActive()) {
       header('Location: /login');
    }

    # Members have nothing to do here
    $user = Session::getUser();
    if($user->isMember()) {
      header('Location: /dashboard');
    }

    //$application = Application::genByUser($user);

    return
      <div class="well">
        <form method="post" action="/apply">
          <div class="form-group">
            <label for="gender" class="control-label">Gender</label>
            <select class="form-control" id="gender" name="gender">
              <option>Male</option>
              <option>Female</option>
              <option>Other / Non-identifying</option>
            </select>
          </div>
          <div class="form-group">
            <label for="year" class="control-label">School Year</label>
            <select class="form-control" id="year" name="year">
              <option>1st Year</option>
              <option>2nd Year</option>
              <option>3rd Year</option>
              <option>4th Year</option>
              <option>Year 5+</option>
            </select>
          </div>
          <div class="form-group">
            <label for="q1" class="control-label">Why do you want to rush Lambda Alpha Nu?</label>
            <textarea class="form-control" rows="3" id="q1" name="q1"></textarea>
          </div>
          <div class="form-group">
            <label for="q2" class="control-label">Talk about yourself in a couple of sentences.</label>
            <textarea class="form-control" rows="3" id="q2" name="q2"></textarea>
          </div>
          <div class="form-group">
            <label for="q3" class="control-label">What is your major and why did you choose it?</label>
            <textarea class="form-control" rows="3" id="q3" name="q3"></textarea>
          </div>
          <div class="form-group">
            <label for="q4" class="control-label">What do you do in your spare time?</label>
            <textarea class="form-control" rows="3" id="q4" name="q4"></textarea>
          </div>
          <div class="form-group">
            <label for="q5" class="control-label">Talk about a current event in technology and why it interests you.</label>
            <textarea class="form-control" rows="3" id="q5" name="q5"></textarea>
          </div>
          <div class="form-group">
            <label for="q6" class="control-label">Impress us.</label>
            <textarea class="form-control" rows="3" id="q6" name="q6"></textarea>
          </div>
          <span class="help-block">All fields are required</span>
          <button type="submit" class="btn btn-default">Submit</button>
        </form>
      </div>;
  }
}
