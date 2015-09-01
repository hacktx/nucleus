<?hh

class Render {
  public static function go(:xhp $content, ?string $controller): void {
    $user = null;
    if(Session::isActive()) {
      $user = Session::getUser();
    }

    print
      <x:frag>
        <nucleus:head />
        <body>
          <nucleus:analytics tracking-id={Config::get('GoogleAnalytics')['tracking_id']} />
          <nucleus:nav-bar user={$user} controller={$controller} />
          {self::getFlash()}
          <nucleus:clouds />
          <div class="container">
            <div class="row col-xs-6 col-xs-offset-3">
              <img class="title-img" src="/img/hacktx.svg" />
            </div>
          </div>
          <div class={'container ' . strtolower($controller)}>
            {$content}
          </div>
        </body>
      </x:frag>;
  }

  private static function getFlash(): ?:div {
    $content = null;
    if(Flash::exists(Flash::ERROR)) {
      $content = 
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get(Flash::ERROR)}
        </div>;
    } elseif (Flash::exists(Flash::SUCCESS)) {
      $content =
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get(Flash::SUCCESS)}
        </div>;
    }

    if (!$content) {
      return null;
    }

    return
      <div class="row">
        <div class="col-md-6 col-md-offset-3">
          {$content}
        </div>
      </div>;
  }
}
