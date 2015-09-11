<?hh // strict

final class :nucleus:dashboard extends :x:element {
  attribute
    string name,
    string status;

  children (:xhp*);

  final protected function render(): :div {
    return
      <div class="col-md-6 col-md-offset-3 text-center">
        <h3>Thanks for applying, {$this->getAttribute('name')}!</h3>
        <p class="prompt-open">Your Application Status Is</p>
        <div class="status">
          <h1><span class="label label-info">{$this->getAttribute('status')}</span></h1>
        </div>
        {$this->getChildren()}
      </div>;
  }
}
