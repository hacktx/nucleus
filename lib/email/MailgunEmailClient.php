<?hh // strict

class MailgunEmailClient extends EmailClient {
  private Mailgun\Mailgun $client;
  public string $domain;
  public string $email;

  public function __construct(string $api_key, string $domain, string $from_email): void {
    $this->client = new Mailgun\Mailgun($api_key);
    $this->domain = $domain;
    $this->email = $from_email;
  }

  public function send(string $to, string $subject, string $body): void {
    $this->client->sendMessage($this->domain, Map {
      'from' => $this->email,
      'to' => $to,
      'subject' => $subject,
      'text' => $body
    });
  }
}
