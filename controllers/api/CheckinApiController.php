<?hh

class CheckinApiController extends BaseController {
  public static function getPath(): string {
    return '/api/checkin';
  }

  public static function post(): Map<string, mixed> {
    $post = getPOSTParams();
    if (!isset($post['email']) || !isset($post['volunteer_id'])) {
      http_response_code(400);
      return Map {"error" => "Missing required parameter"};
    }

    DB::query("SELECT * FROM volunteer WHERE id=%s", $post['volunteer_id']);
    if (DB::count() === 0) {
      http_response_code(401);
      return Map {"error" => "Volunteer ID not found"};
    }

    $user = User::genByEmail((string) $post['email']);
    if (!$user) {
      http_response_code(404);
      return Map {"error" => "User not found"};
    }

    if ($user->getRoles()->contains(UserRole::CheckedIn)) {
       http_response_code(400);
       return Map {"error" => "User is already checked in"};
    }

    Roles::insert(UserRole::CheckedIn, $user->getID());

    return Map {"success" => "User checked in successfully"};
  }
}
