<?hh // strict

class MembersNewController extends BaseController {
  public static function getPath(): string {
    return '/members/new';
  }

  public static function get(): :xhp {
    return
      <x:js-scope>
        <nucleus:members-table />
      </x:js-scope>;
  }
}
