<?hh // strict

class MembersNewController extends BaseController {
  public static function getPath(): string {
    return '/members/new';
  }

  public static function get(): :xhp {
    $members = new Vector(DB::query("SELECT * FROM users ORDER BY created ASC"));

    return
      <x:js-scope>
        <nucleus:members-table members={$members} />
      </x:js-scope>;
  }
}
