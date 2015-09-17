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
    if (DB::count() === 0) {
      http_response_code(401);
      return Map {"error" => "Volunteer ID not found"};
    }

    $user = User::genByEmail((string) $get['email']);
    if (!$user) {
      http_response_code(404);
      return Map {"error" => "User not found"};
    }

    $data = Map {
      'name' => $user->getFirstName().' '.$user->getLastName(),
      'email' => $user->getEmail(),
      'age' => $user->getAge(),
      'confirmed' => ($user->getStatus() === UserState::Confirmed),
      'checked_in' => false,
    };

    return $data;
  }
}
