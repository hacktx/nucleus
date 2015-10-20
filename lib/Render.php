<?hh // strict

class Render {
  public static function go(
    XHPRoot $content,
    string $controller,
    ?string $title,
  ): void {
    $user = null;
    if (Session::isActive()) {
      $user = Session::getUser();
    }

    print
      <nucleus:layout user={$user} controller={$controller} title={$title}>
        {$content}
      </nucleus:layout>
    ;
  }
}
