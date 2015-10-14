<?hh // strict

final class :nucleus:settings extends :x:element {
  use XHPHelpers;
  use XHPReact;

  attribute
    :xhp:html-element,
    Map<string, bool> settings @required;

  protected function render(): XHPRoot {
    $this->constructReactInstance(
      'Settings',
      Map {'settings' => $this->:settings }
    );
    
    return <div id={$this->getID()} />;
  }
}
