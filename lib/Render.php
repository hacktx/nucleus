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
          <nucleus:nav-bar user={$user} controller={$controller}/>
          <div class="container">
            {self::getFlash()}
            {$content}
          </div>
        </body>
      </x:frag>;
  }

  private static function getFlash(): ?:div {
    if(Flash::exists(Flash::ERROR)) {
      return
        <div class="alert alert-danger alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get(Flash::ERROR)}
        </div>;
    } elseif (Flash::exists(Flash::SUCCESS)) {
      return
        <div class="alert alert-success alert-dismissible" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          {Flash::get(Flash::SUCCESS)}
        </div>;
    }
    return null;
  }
}
