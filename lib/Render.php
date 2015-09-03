<?hh

class Render {
  public static function go(:xhp $content, ?string $controller): void {
    $user = null;
    if(Session::isActive()) {
      $user = Session::getUser();
    }

    print
      <nucleus:layout user={$user} controller={$controller}>
        {$content}
      </nucleus:layout>;
  }
}
