<?hh // strict

class Email {
  public static ?EmailClient $client = null;

  public static function initialize(EmailClient $client): void {
    self::$client = $client;
  }

  public static function send(
    string $to,
    string $subject,
    string $body
  ): void {
    invariant(self::$client !== null, 'Client must be initialized');
    self::$client->send($to, $subject, $body);
  }
}
