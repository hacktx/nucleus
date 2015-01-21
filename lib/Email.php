<?hh

class Email {
  public static Mailgun $mg;
  public static string $domain;
  public static string $from;

  public static function send(
    string $list,
    string $subject,
    string $body
  ): void {
    self::$mg->sendMessage(self::$domain, array(
      'from' => self::$from,
      'to' => $list . '@' . self::$domain,
      'subject' => $subject,
      'text' => $body
    ));
  }

  public static function subscribe(string $list, User $user): void {
    self::$mg->post('lists/' . $list . '/members' , array(
      'address' => $user->getEmail(),
      'name' => $user->getFirstName() . ' ' . $user->getLastName(),
      'subscribed' => true
    ));
  }

  public static function getLists(): array {
    $result = self::$mg->get('lists');
    return $result->http_response_body->items;
  }
}
