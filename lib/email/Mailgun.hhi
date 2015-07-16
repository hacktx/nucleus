<?hh // decl

namespace Mailgun {
  class Mailgun {
    public function __construct(string $api_key): void;
    public function sendMessage(string $domain, Map<string, string> $email): void;
  }
}
