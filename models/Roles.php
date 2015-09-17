<?hh

enum UserRole: string {
  Superuser = 'superuser';
  Organizer = 'organizer';
  Flagged = 'flagged';
  CheckedIn = 'checked-in';
}

class Roles {
  public static function insert(UserRole $role, int $user_id): void {
    DB::insert('roles', array(
      'role' => $role,
      'user_id' => $user_id
    ));
  }

  public static function delete(UserRole $role, int $user_id): void {
    DB::delete('roles', 'user_id=%s AND role=%s', $user_id, $role);
  }

  public static function getRoles(int $user_id): Set<UserRole> {
    $query = DB::query("SELECT role FROM roles WHERE user_id=%s", $user_id);
    $roles = array_map(function($value) {
      return UserRole::assert($value['role']);
    }, $query);
    return new Set($roles);
  }
}
