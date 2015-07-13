<?hh

class ApplyController extends BaseController {
  public static function getPath(): string {
    return '/apply';
  }

  public static function getConfig(): ControllerConfig {
    return new ControllerConfig();
  }

  public static function get(): :xhp {
    $user = Session::getUser();
    $application = Application::genByUser($user);

    $disabled =
      $application->isSubmitted() || !Settings::get('applications_open');

    $alert = null;
    if($application->isSubmitted()) {
      $alert =
        <div class="alert alert-info" role="alert">
          Your application has been submitted and can no longer be edited.
        </div>;
    } else if (!Settings::get('applications_open')) {
      $alert =
        <div class="alert alert-info" role="alert">
          Applications are currently closed
        </div>;
    }

    return
      <x:frag>
        {$alert}
        <div class="well">
          <form method="post" action="/apply">
            <fieldset disabled={$disabled}>
              <div class="form-group">
                <label for="gender" class="control-label">Gender</label>
                <select class="form-control" id="gender" name="gender">
                  <option selected={$application->getGender() === "Male"}>
                    Male
                  </option>
                  <option selected={$application->getGender() === "Female"}>
                    Female
                  </option>
                  <option selected={$application->getGender() === "Other / Non-identifying"}>
                    Other / Non-identifying
                  </option>
                </select>
              </div>
              <div class="form-group">
                <label for="year" class="control-label">School Year</label>
                <select class="form-control" id="year" name="year">
                  <option selected={$application->getYear() === "1st Year"}>1st Year</option>
                  <option selected={$application->getYear() === "2nd Year"}>2nd Year</option>
                  <option selected={$application->getYear() === "3rd Year"}>3rd Year</option>
                  <option selected={$application->getYear() === "4th Year"}>4th Year</option>
                  <option selected={$application->getYear() === "Year 5+"}>Year 5+</option>
                </select>
              </div>
              <div class="form-group">
                <label for="q1" class="control-label">Why do you want to rush Lambda Alpha Nu?</label>
                <textarea class="form-control" rows={3} id="q1" name="q1">
                  {$application->getQ1()}
                </textarea>
              </div>
              <div class="form-group">
                <label for="q2" class="control-label">
                  Talk about yourself in a couple of sentences.
                </label>
                <textarea class="form-control" rows={3} id="q2" name="q2">
                  {$application->getQ2()}
                </textarea>
              </div>
              <div class="form-group">
                <label for="q3" class="control-label">
                  What is your major and why did you choose it?
                </label>
                <textarea class="form-control" rows={3} id="q3" name="q3">
                  {$application->getQ3()}
                </textarea>
              </div>
              <div class="form-group">
                <label for="q4" class="control-label">
                  What do you do in your spare time?
                </label>
                <textarea class="form-control" rows={3} id="q4" name="q4">
                  {$application->getQ4()}
                </textarea>
              </div>
              <div class="form-group">
                <label for="q5" class="control-label">
                  Talk about a current event in technology and why it interests you.
                </label>
                <textarea class="form-control" rows={3} id="q5" name="q5">
                  {$application->getQ5()}
                </textarea>
              </div>
              <div class="form-group">
                <label for="q6" class="control-label">Impress us.</label>
                <textarea class="form-control" rows={3} id="q6" name="q6">
                  {$application->getQ6()}
                </textarea>
              </div>
              <span class="help-block">All fields are required</span>
              <div class="btn-toolbar">
                <button type="submit" class="btn btn-default">Save</button>
                <button type="submit" name="submit" value="1" class="btn btn-primary">Submit</button>
              </div>
            </fieldset>
          </form>
        </div>
      </x:frag>;
  }

  public static function post(): void {
    if(!Settings::get('applications_open')) {
      Flash::set('error', 'Applications are currently closed');
      Route::redirect('/dashboard');
    }

    $user = Session::getUser();
    $application = Application::upsert(
      $user->getID(),
      $_POST['gender'],
      $_POST['year'],
      $_POST['q1'],
      $_POST['q2'],
      $_POST['q3'],
      $_POST['q4'],
      $_POST['q5'],
      $_POST['q6']
    );

    if(isset($_POST['submit'])) {
      if($_POST['q1'] === '' || $_POST['q2'] === '' || $_POST['q3'] === '' ||
         $_POST['q4'] === '' || $_POST['q5'] === '' || $_POST['q6'] === '') {
        Flash::set('error', 'All fields are required');
        Route::redirect('/apply');
      }
      $application->submit();
    }

    Route::redirect('/apply');
  }
}
