<?hh // strict

class SendGridEmailClient extends EmailClient {
  private SendGrid $client;
  private string $email;

  public function __construct(string $api_key, string $from_email): void {
    $this->client = new SendGrid($api_key);
    $this->email = $from_email;
  }

  public function send(string $to, string $subject, string $body): void {
    $email = new SendGrid\Email();
    $email->addTo($to)
          ->setFrom($this->email)
          ->setFromName("Team HackTX")
          ->setSubject($subject)
          ->setHtml($body);

    $this->client->send($email);
    return;
  }
}
