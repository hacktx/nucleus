<?hh

class AppReview {
  public static function upsert(
    string $comments,
    int $rating,
    User $user,
    Application $application
  ): void {
    DB::query("SELECT * FROM reviews WHERE user_id=%s AND application_id=%s", $user->getID(), $application->getID());

    if(DB::count() != 0) {
      DB::update('reviews', array(
        'comments' => $comments,
        'rating' => $rating
      ), 'user_id=%s AND application_id=%s', $user->getID(), $application->getID());
    } else {
      DB::insert('reviews', array(
        'comments' => $comments,
        'rating' => $rating,
        'user_id' => $user->getID(),
        'application_id' => $application->getID()
      ));
    }
  }

  public function getComments(): ?string {
    return isset($this->comments) ? $this->comments : null;
  }

  public function getRating(): ?int {
    return isset($this->rating) ? (int)$this->rating : null;
  }

  public static function genByUserAndApp(User $user, Application $application): AppReview {
    $query = DB::queryFirstRow("SELECT * FROM reviews WHERE user_id=%s AND application_id=%s", $user->getID(), $application->getID());
    if(!$query) {
      return new AppReview();
    }
    $review = new AppReview();
    $review->comments = $query['comments'];
    $review->rating = $query['rating'];
    $review->user_id = $query['user_id'];
    $review->application_id = $query['application_id'];
    return $review;
  }
}
