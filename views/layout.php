<?hh // strict 

final class :nucleus:layout extends :x:element {
  attribute
    User user,
    string controller,
    ?string title;

  children (:xhp);

  final protected function render(): XHPRoot {
    return
      <html>
        <nucleus:head title={$this->getAttribute('title')}/>
        <body>
          <nucleus:analytics tracking-id={Config::get('GoogleAnalytics')['tracking_id']} />
          <nucleus:nav-bar user={$this->getAttribute('user')} controller={$this->getAttribute('controller')} />
          <nucleus:flash />
          <div class="container">
            {$this->getChildren()}
          </div>
        </body>
      </html>;
  }
}
