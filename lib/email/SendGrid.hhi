<?hh // decl

class SendGrid {
  public function __construct(string $api_key);
  public function send(SendGrid\Email $email);
}

namespace SendGrid {
  class Email {
    public function addTo(string $email): this;
    public function setFrom(string $email): this;
    public function setSubject(string $subject): this;
    public function setText(string $text): this;
    public function setHtml(string $html): this;
  }
}
