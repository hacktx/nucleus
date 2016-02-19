<?hh // strict

final class :nucleus:head extends :x:element {
  attribute string title;

  final protected function render(): :head {
    $title = "MyFreetail";
    if ($this->:title !== null) {
      $title = $this->:title." | MyFreetail";
    }

    return
      <head>
        <meta charset="UTF-8" />
        <meta
          name="viewport"
          content="width=device-width, initial-scale=1"
        />
        <title>{$title}</title>
        <link
          rel="stylesheet"
          type="text/css"
          href="/css/bootstrap.min.css"
        />
        <link
          rel="icon"
          type="image/png"
          sizes="32x32"
          href="/img/favicon-32x32.png"
        />
        <link
          rel="icon"
          type="image/png"
          sizes="96x96"
          href="/img/favicon-96x96.png"
        />
        <link
          rel="icon"
          type="image/png"
          sizes="16x16"
          href="/img/favicon-16x16.png"
        />
        <script
          src=
            "https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js">
        </script>
        <script
          src=
            "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js">
        </script>
        <script
          src=
            "https://cdnjs.cloudflare.com/ajax/libs/list.js/1.1.1/list.min.js">
        </script>
        <script src="/js/bundle.js"></script>
      </head>;
  }
}
