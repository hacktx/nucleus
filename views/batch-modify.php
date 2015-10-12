<?hh // strict

final class :nucleus:batch-modify extends :x:element {
  children (:option+);

  final protected function render(): :form {
    return
      <form method="post">
        <div class="form-inline form-group">
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">Move</div>
              <select class="form-control" name="place" id="place">
                <option>First</option>
                <option>Last</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <input
              name="number"
              type="text"
              class="form-control"
              id="number-accept"
              placeholder="600"
            />
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">From</div>
              <select class="form-control" name="from" id="from">
                {$this->getChildren()}
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="input-group">
              <div class="input-group-addon">To</div>
              <select class="form-control" name="to" id="to">
                {$this->getChildren()}
              </select>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label for="subject">Email Subject</label>
          <input
            type="text"
            class="form-control"
            id="subject"
            name="subject"
          />
        </div>
        <div class="form-group">
          <label for="email-input">Email Contents</label>
          <textarea class="form-control" rows={3} name="email" />
        </div>
        <button type="submit" class="btn btn-default">Submit</button>
      </form>;
  }
}
