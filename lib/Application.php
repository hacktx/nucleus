<?hh

class Application {

  public static function upsert(
    $user_id,
    $gender,
    $year,
    $q1,
    $q2,
    $q3,
    $q4,
    $q5,
    $q6
  ): Application {
    # Make sure the user doesn't already have an application active
    $query = DB::query("SELECT * FROM applications WHERE user_id=%s", $user_id);

    if(DB::count() != 0) {
      # The user has submitted their app, don't allow them to update
      if($query['submitted']) {
        header('Location: /dashboard');
      }

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
        'q6' => $q6,
        'status' => 1
      ));
    }

    $query = DB::queryFirstRow("SELECT * FROM applications WHERE user_id=%s", $user_id);
    return self::createFromQuery($query);
  }

  public function submit(): void {
    DB::update('applications', array(
      'status' => 2
    ), 'id', $this->id);
  }

  public function getID(): int {
    return $this->id;
  }

  public function getUserID(): int {
    return (int)$this->user_id;
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

  public function isStarted(): bool {
    return $this->status == 1;
  }

  public function isSubmitted(): bool {
    return $this->status == 2;
  }

  public static function genByUser(User $user): Application {
    return self::constructFromQuery('user_id', $user->getID());
  }

  public static function genByID(int $app_id): Application {
    return self::constructFromQuery('id', $app_id);
  }

  private static function constructFromQuery($field, $query): Application {
    $query = DB::queryFirstRow("SELECT * FROM applications WHERE " . $field . "=%s", $query);
    if($query === null) {
      return new Application();
    }
    return self::createFromQuery($query);
  }

  private static function createFromQuery(array $query): Application {
    $application = new Application();
    $application->id = $query['id'];
    $application->user_id = $query['user_id'];
    $application->gender = $query['gender'];
    $application->year = $query['year'];
    $application->q1 = $query['q1'];
    $application->q2 = $query['q2'];
    $application->q3 = $query['q3'];
    $application->q4 = $query['q4'];
    $application->q5 = $query['q5'];
    $application->q6 = $query['q6'];
    $application->status = $query['status'];
    return $application;
  }
}
