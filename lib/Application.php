<?hh

class Application {

  public static function create(
    $user_id,
    $gender,
    $year,
    $q1,
    $q2,
    $q3,
    $q4,
    $q5,
    $q6
  ): void {
    # Make sure the user doesn't already have an application active
    DB::query("SELECT * FROM applications WHERE user_id=%s", $user_id);

    if(DB::count() != 0) {
      # An application exists, just update it
      DB::update('applications', array(
        'gender' => $gender,
        'year' => $year,
        'q1' => $q1,
        'q2' => $q2,
        'q3' => $q3,
        'q4' => $q4,
        'q5' => $q5,
        'q6' => $q6
      ), 'user_id=%s', $user_id);
    } else {
      # Insert the application
      DB::insert('applications', array(
        'user_id' => $user_id,
        'gender' => $gender,
        'year' => $year,
        'q1' => $q1,
        'q2' => $q2,
        'q3' => $q3,
        'q4' => $q4,
        'q5' => $q5,
        'q6' => $q6
      ));
    }
  }

  public function getGender(): ?string {
    return isset($this->gender) ? $this->gender : null;
  }

  public function getYear(): ?string {
    return isset($this->year) ? $this->year : null;
  }

  public function getQ1(): ?string {
    return isset($this->q1) ? $this->q1 : null;
  }

  public function getQ2(): ?string {
    return isset($this->q2) ? $this->q2 : null;
  }

  public function getQ3(): ?string {
    return isset($this->q3) ? $this->q3 : null;
  }

  public function getQ4(): ?string {
    return isset($this->q4) ? $this->q4 : null;
  }

  public function getQ5(): ?string {
    return isset($this->q5) ? $this->q5 : null;
  }

  public function getQ6(): ?string {
    return isset($this->q6) ? $this->q6 : null;
  }

  public static function genByUser(User $user): ?Application {
    $query = DB::queryFirstRow("SELECT * FROM applications WHERE user_id=%s", $user->getID());
    if($query === null) {
      return new Application();
    }
    return self::createFromQuery($query);
  }

  private static function createFromQuery(array $query): Application {
    $application = new Application();
    $application->gender = $query['gender'];
    $application->year = $query['year'];
    $application->q1 = $query['q1'];
    $application->q2 = $query['q2'];
    $application->q3 = $query['q3'];
    $application->q4 = $query['q4'];
    $application->q5 = $query['q5'];
    $application->q6 = $query['q6'];
    return $application;
  }
}
