<?hh // strict 

final class :nucleus:layout extends :x:element {
  attribute
    User user,
    string controller;

  children (:xhp);

  final protected function render(): XHPRoot {
    return
      <html>
        <nucleus:head />
        <body>
          <nucleus:analytics tracking-id={Config::get('GoogleAnalytics')['tracking_id']} />
          <nucleus:nav-bar user={$this->getAttribute('user')} controller={$this->getAttribute('controller')} />
          <nucleus:flash />
          <nucleus:clouds />
          <div class="container">
            <div class="row col-xs-6 col-xs-offset-3">
              <img class="title-img" src="/img/hacktx.svg" />
            </div>
          </div>
          <div class={'container ' . strtolower($this->getAttribute('controller'))}>
            {$this->getChildren()}
          </div>
        </body>
      </html>;
  }
}
