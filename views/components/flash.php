<?hh // strict

final class :nucleus:flash extends :x:element {
  final protected function render(): :div {
    $content = null;
    if (Flash::exists(Flash::ERROR)) {
      $content =
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          {Flash::get(Flash::ERROR)}
        </div>;
    } else if (Flash::exists(Flash::SUCCESS)) {
      $content =
        <div class="alert alert-success alert-dismissible" role="alert">
          <button
            type="button"
            class="close"
            data-dismiss="alert"
            aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          {Flash::get(Flash::SUCCESS)}
        </div>;
    }

    if (!$content) {
      return <div />;
    }

    return
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          {$content}
        </div>
      </div>;
  }
}
