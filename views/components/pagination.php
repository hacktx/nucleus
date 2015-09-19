<?hh // strict

final class :nucleus:pagination extends :x:element {
  attribute URIBuilder uri-builder, int page, int max;

  final protected function render(): :nav {
    $uri_builder = $this->getAttribute('uri-builder');
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
          <a href={$uri_builder->setParam('page', $i)->getURI()}>{$i}</a>
        </li>;
    }

    $beginning =
      <li class={$page < 3 ? "disabled" : ""}>
        <a
          href={$uri_builder->setParam('page', 0)->getURI()}
          aria-label="Beginning">
          <span aria-hidden="true">&laquo;</span>
        </a>
      </li>;

    $back_page = $page < 5 ? 0 : $page - 5;

    $back =
      <li class={$page < 3 ? "disabled" : ""}>
        <a
          href={$uri_builder->setParam('page', $back_page)->getURI()}
          aria-label="Previous">
          <span aria-hidden="true">&lsaquo;</span>
        </a>
      </li>;

    $next_page = $max - $page > 5 ? $page + 5 : $max;

    $next =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a
          href={$uri_builder->setParam('page', $next_page)->getURI()}
          aria-label="Next">
          <span aria-hidden="true">&rsaquo;</span>
        </a>
      </li>;

    $end =
      <li class={$max - $page < 3 ? "disabled" : ""}>
        <a
          href={$uri_builder->setParam('page', $max)->getURI()}
          aria-label="End">
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
}
