<?hh

enum UserRole: string {
  Superuser = 'superuser';
  Organizer = 'organizer';
}

class Roles {
  public static function insert(string $role, int $user_id): void {
    DB::insert('roles', array(
      'role' => $role,
      'user_id' => $user_id
    ));
  }

  public static function delete(string $role, int $user_id): void {
    DB::delete('roles', 'user_id=%s AND role=%s', $user_id, $role);
  }

  public static function getRoles(int $user_id): array {
    $query = DB::query("SELECT role FROM roles WHERE user_id=%s", $user_id);
    $roles = array_map(function($value) {
      return $value['role'];
    }, $query);
    return $roles;
  }
}
