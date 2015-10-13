<?hh // strict

final class :nucleus:members-table extends :x:element {
  use XHPHelpers;
  use XHPReact;

  attribute :xhp:html-element;

  protected function render(): XHPRoot {
    // Self-explanatory :)
    $this->constructReactInstance(
      'MembersTable',
      Map {'someAttribute' => 'test' }
    );
    
    return <div id={$this->getID()} />;
  }
}
