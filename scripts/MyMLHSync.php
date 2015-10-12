<?hh

require 'vendor/autoload.php';

class MyMLHSync {
  public static function run(): void {
    $configs = parse_ini_file('config.ini', true);

    $response = file_get_contents(
      'https://my.mlh.io/api/v1/users?client_id='.
      $configs['MLH']['client_id'].
      '&secret='.
      $configs['MLH']['client_secret'],
    );
    $json = json_decode($response);

    DB::$user = $configs['DB']['user'];
    DB::$password = $configs['DB']['password'];
    DB::$dbName = $configs['DB']['name'];
    DB::$port = $configs['DB']['port'];

    foreach ($json->data as $user) {
      DB::query("SELECT * FROM users WHERE id=%i", $user->id);
      if (DB::count() == 0) {
        continue;
      }

      DB::update(
        'users',
        Map {
          'email' => $user->email,
          'fname' => $user->first_name,
          'lname' => $user->last_name,
          'graduation' => $user->graduation,
          'major' => $user->major,
          'shirt_size' => $user->shirt_size,
          'dietary_restrictions' => $user->dietary_restrictions,
          'special_needs' => $user->special_needs,
          'birthday' => $user->date_of_birth,
          'gender' => $user->gender,
          'phone_number' => $user->phone_number,
          'school' => $user->school->name,
        },
        'id=%i',
        $user->id,
      );
    }
  }
}

MyMLHSync::run();
