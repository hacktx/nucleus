<?hh

class UserApiController extends BaseController {
  public static function getPath(): string {
    return '/api/user';
  }

  public static function get(): Map<string, mixed> {
    $get = getGETParams();
    if (!isset($get['email']) || !isset($get['volunteer_id'])) {
      http_response_code(400);
      return Map {"error" => "Missing required parameter"};
    }

    DB::query("SELECT * FROM volunteer WHERE id=%s", $get['volunteer_id']);
    if(DB::count() === 0) {
      http_response_code(401);
      return Map {"error" => "Volunteer ID not found"};
    }

    $user = DB::queryFirstRow("SELECT * FROM users WHERE email=%s", $get['email']);
    if(DB::count() === 0) {
      http_response_code(404);
      return Map {"error" => "User not found"};
    }

    $birthday = new DateTime($user['birthday']);
    $age = $birthday->diff(new DateTime('today'))->y;

    $data = Map {
      'name' => $user['fname'] . ' ' . $user['lname'],
      'email' => $user['email'],
      'age' => $age,
      'checked_in' => false,
    };

    return $data;
  }
}
