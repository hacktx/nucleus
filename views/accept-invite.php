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
              <p class="accept-sub-layer">Signing this online liability form will improve and speed up the checkin process.</p>
              <div class="liability-btn">
                <a href="#" class="btn btn-panel">
                SIGN LIABILITY FORM
                </a>
              </div>

              <hr />

              <h4>RESUME UPLOAD</h4>
              <p class="accept-sub-layer">You are awesome and we want to show that to our sponsors. Upload your resume and we will share it with our sponsors.</p>
              <div class="form-group btn btn-panel resume">
                <span>Upload</span>
                <input type="file" />
              </div>

              <hr />

              <h4>ADDITIONAL INFORMATION</h4>
              <p class="accept-sub-layer">We would love to understand the demographic breakdown of our hackers to better inform our outreach efforts. Answers to these questions will not be taken into consideration during admissions. Do not feel pressured if you "Prefer not to state" your answers.</p>

              <h5 class="accept-sub-layer">Year In School</h5>
              <form class="school-year-select accept-sub-layer">
                <select>
                  <option value="" style="display:none;"></option>
                  <option>Freshman</option>
                  <option>Sophomore</option>
                  <option>Junior</option>
                  <option>Senior</option>
                  <option>Graduate</option>
                </select>
              </form>

              <h5 class="accept-sub-layer">Racial Identity</h5>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> American Indian or Alaskan Native</label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> Asian or Pacific Islander</label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> Black</label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> Hispanic</label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> White</label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> Prefer not to state</label>
              </div>
              <div class="accept-sub-layer">
                <label><input type="text" name="otherrace" placeholder="Other"/></label>
              </div>
        
              <h5 class="accept-sub-layer">Is This Your First Hackathon?</h5>
              <div class="checkbox accept-sub-layer">
                <label><input type="checkbox" /> Yes</label>
              </div>

              <hr />

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
