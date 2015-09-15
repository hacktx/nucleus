<?hh // strict

final class :nucleus:accept-invite extends :x:element {
  attribute User user;

  final protected function render(): XHPRoot {
    $user = $this->getAttribute('user');
    return
      <form class="form" method="post" enctype="multipart/form-data">
        <div class="col-md-8 col-md-offset-2">
          <h2 style="text-align: center;">
            Congrats, {$user->getFirstName()}! Just A Couple Things...
          </h2>
          <div class="panel panel-default">
            <div class="panel-body accept-invite-panel">
              <h4>RESUME UPLOAD</h4>
              <p class="accept-sub-layer">
                You are awesome and we want to show that to our sponsors.
                Upload your resume and we will share it with our sponsors. You
                can do this later if you want. PDFs only.
              </p>
              <div class="form-group resume">
                <input type="file" name="resume" /> 
              </div>
              <hr />

              <h4>ADDITIONAL INFORMATION</h4>
              <p class="accept-sub-layer">
                We would love to understand the demographic breakdown of our
                hackers so we can see how we’ve been doing over time. Answers
                to these questions will be sent anonymously and will at no
                point be attached to identifying information (such as name,
                email, resume, etc.). Please select "Prefer not to say" if you
                do not wish to provide this information.
              </p>
              <h5 class="accept-sub-layer">Year In School</h5>
              <div class="school-year-select accept-sub-layer">
                <select name="year">
                  <option>Select one</option>
                  <option value="freshman">Freshman</option>
                  <option value="sophomore">Sophomore</option>
                  <option value="junior">Junior</option>
                  <option value="senior">Senior</option>
                  <option value="graduate">Graduate</option>
                  <option>Prefer not to say</option>
                </select>
              </div>
              <h5 class="accept-sub-layer">Racial Identity</h5>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input
                    type="checkbox"
                    name="race[]"
                    value="American Indian or Alaskan Native"
                  />
                  American Indian or Alaskan Native
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input type="checkbox" name="race[]" value="Asian" /> Asian
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input
                    type="checkbox"
                    name="race[]"
                    value="Black or African American"
                  />
                  Black or African American
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input
                    type="checkbox"
                    name="race[]"
                    value="Native Hawaiian or Other Pacific Islander"
                  />
                  Native Hawaiian or Other Pacific Islander
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input type="checkbox" name="race[]" value="Hispanic" />
                  Hispanic
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input
                    type="checkbox"
                    name="race[]"
                    value="White or Caucasian"
                  />
                  White or Caucasian
                </label>
              </div>
              <div class="checkbox accept-sub-layer opt-out">
                <label>
                  <input type="checkbox" onclick="optOut()" /> Prefer not to
                  say
                </label>
              </div>
              <div class="checkbox accept-sub-layer">
                <label>
                  <input
                    type="checkbox"
                    name="race[]"
                    value="other"
                    onclick={"$(\".otherrace\").toggleClass(\"hidden\")"}
                  />
                  Other
                </label>
              </div>
              <div class="accept-sub-layer otherrace hidden">
                <label>
                  <input type="text" name="otherrace" placeholder="Other" />
                </label>
              </div>
              <h5 class="accept-sub-layer">Is This Your First Hackathon?</h5>
              <div class="radio accept-sub-layer">
                <label>
                  <input
                    type="radio"
                    name="first-hackathon"
                    id="first-hackathon"
                    value="yes"
                  />
                  Yes
                </label>
              </div>
              <div class="radio accept-sub-layer">
                <label>
                  <input
                    type="radio"
                    name="first-hackathon"
                    id="first-hackathon"
                    value="no"
                  />
                  No
                </label>
              </div>
              <div class="radio accept-sub-layer">
                <label>
                  <input
                    type="radio"
                    name="first-hackathon"
                    id="first-hackathon"
                    value="optout"
                  />
                  Prefer not to say
                </label>
              </div>
              <hr />

              <h4>CODE OF CONDUCT*</h4>
              <div class="checkbox accept-sub-layer opt-out">
                <label>
                  <input type="checkbox" name="coc" /> I will at all times
                  abide by and conform to the
                  <a
                    target="_blank"
                    href="http://static.mlh.io/docs/mlh-code-of-conduct.pdf">
                    MLH Code of Conduct
                  </a>
                  while at HackTX.
                </label>
              </div>
            </div>
          </div>
          <p class="text-center">
            <button type="submit" name="deny" class="btn btn-secondary">
              I CAN'T MAKE IT
            </button>
            &nbsp;
            <button type="submit" name="accept" class="btn btn-primary">
              I WILL BE THERE!
            </button>
          </p>
        </div>
        <script src="/js/accept.js"></script>
      </form>;
  }
}
