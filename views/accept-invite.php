<?hh // strict

final class :nucleus:accept-invite extends :x:element {
  attribute User user;

  final protected function render(): XHPRoot {
    return
      <x:frag>
        <div class="col-md-8 col-md-offset-2">
          <h2 style="text-align: center;">Congrats! Just A Couple Things...</h2>
          <div class="panel panel-default">
            <div class="panel-body accept-invite-panel">
              <h4>LIABILITY FORM</h4>
              <p>Signing this online liability form will improve and speed up the checkin process.</p>
              <div class="liability-btn">
                <a href="#" class="btn btn-panel">
                SIGN LIABILITY FORM
                </a>
              </div>

              <h4>RESUME UPLOAD</h4>
              <p>You are awesome and we want to show that to our sponsors. Upload your resume and we will share it with our sponsors.</p>
              <div class="form-group">
                <input type="file" id="resume" />
                <a href="#" class="btn btn-panel">
                Choose file
                </a>
              </div>

              <h4>CODE OF CONDUCT*</h4>
              <div class="checkbox">
                <label><input type="checkbox" /> I will at all times abide by and conform to the <a href="http://static.mlh.io/docs/mlh-code-of-conduct.pdf">Major League Hacking Code of Conduct</a> while at HackTX.</label>
              </div>
            </div>
          </div>
          <div class="text-right">
            <form
              action={AcceptInviteController::getPath()}
              method="post"
              style="display: inline-block;">
              <button type="submit" class="btn btn-secondary">I CANT MAKE IT</button>
            </form>
            <form
              action={AcceptInviteController::getPath()}
              method="post"
              style="display: inline-block;">
              <button type="submit" class="btn btn-primary">I WILL BE THERE!</button>
            </form>
          </div>
        </div>
      </x:frag>;
  }
}
