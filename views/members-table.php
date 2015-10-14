<?hh // strict

final class :nucleus:members-table extends :x:element {
  use XHPHelpers;
  use XHPReact;

  attribute
    :xhp:html-element,
    Vector<Map<string, mixed>> members @required;

  protected function render(): XHPRoot {
    $this->constructReactInstance(
      'MembersTable',
      Map {'members' => $this->:members }
    );
    
    return <div id={$this->getID()} />;
  }
}
