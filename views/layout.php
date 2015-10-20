<?hh // strict

final class :nucleus:layout extends :x:element {
  attribute
    User user,
    string controller,
    string title;

  children (:xhp);

  final protected function render(): XHPRoot {
    return
      <html>
        <nucleus:head title={$this->:title} />
        <body>
          <nucleus:analytics
            tracking-id={Config::get('GoogleAnalytics')['tracking_id']}
          />
          <nucleus:nav-bar
            user={$this->:user}
            controller={$this->:controller}
          />
          <nucleus:flash />
          <div class="container">
            {$this->getChildren()}
          </div>
        </body>
      </html>;
  }
}
