<?hh // strict

final class :nucleus:pagination extends :x:element {
  attribute string path, UserState filter, int page, int max;

  final protected function render(): :nav {
    $path = $this->getAttribute('path');
    $filter = $this->getAttribute('filter');
    $page = $this->getAttribute('page');
    $max = $this->getAttribute('max');

    $buttons = Vector {};
    for (
      $i = ($page < 2 ? 0 : $page - 2);
      $i < ($page < 2 ? 5 : $page + 3);
      $i++
    ) {
      if ($i > $max) {
        break;
      }

      $buttons[] =
        <li class={$i == $page ? "active" : ""}>
          <a href={self::getLink($path, $i, $filter)}>{$i}</a>
        </li>;
    }

    $beginning =
      <li class={$page < 3 ? "disabled" : ""}>
        <a href={self::getLink($path, 0, $filter)} aria-label="Beginning">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>;

    $back =
      <li class={$page < 3 ? "disabled" : ""}>
        <a
          href={self::getLink($path, $page < 5 ? 0 : $page - 5, $filter)}
          aria-label="Previous">
          <span aria-hidden="true">&lsaquo;</span>
        </a>
      </li>;

    $next =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a
          href=
            {self::getLink(
              $path,
              $max - $page > 5 ? $page + 5 : $max,
              $filter,
            )}
          aria-label="Next">
          <span aria-hidden="true">&rsaquo;</span>
        </a>
      </li>;

    $end =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a href={self::getLink($path, $max, $filter)} aria-label="End">
          <span aria-hidden="true">&raquo;</span>
        </a>
      </li>;

    return
      <nav>
        <ul class="pagination">
          {$beginning}
          {$back}
          {$buttons}
          {$next}
          {$end}
        </ul>
      </nav>;
  }

  private static function getLink(
    string $path,
    int $page,
    ?UserState $filter,
  ): string {
    $url = $path."?page=".$page;
    if ($filter !== null) {
      $url = $url."&filter=".$filter;
    }

    return $url;
  }
}
