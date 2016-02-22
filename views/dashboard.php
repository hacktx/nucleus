<?hh // strict

final class :nucleus:dashboard extends :x:element {
  attribute string name @required, string status;

  children (:xhp*);

  final protected function render(): :div {
    $status = $this->:status;
    $application_status = null;
    if ($status === null) {
      return
      <div class="col-md-6 col-md-offset-3 text-center">
        <h3>Thanks for applying, {$this->:name}!</h3>
        {$this->getChildren()}
      </div>;
    }

    return
      <div class="col-md-6 col-md-offset-3 text-center">
        <h3>Thanks for applying, {$this->:name}!</h3>
        {$application_status}
        {$this->getChildren()}
      </div>;
  }
}
