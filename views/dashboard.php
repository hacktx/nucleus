<?hh

final class :nucleus:dashboard extends :x:element {
  attribute
    string name,
    string status,
    string email;

  final protected function render(): :div {
    return
      <div class="col-md-6 col-md-offset-3 text-center">
        <h3>Thanks for applying, {$this->getAttribute('name')}!</h3>
        <p class="prompt-open">Your Application Status Is</p>
        <div class="status">
          <h1><span class="label label-info">{$this->getAttribute('status')}</span></h1>
        </div>
        <p class="info">Acceptances will roll out in ~7 days. If accepted, you will receive a confirmation email at {$this->getAttribute('email')} with further instructions.</p>
        <div class="footer">
          <p>Can't make it? <a href={DeleteAccountController::getPath()}>Cancel My Application</a></p>
        </div>
      </div>;
  }
}
