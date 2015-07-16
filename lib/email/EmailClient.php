<?hh // strict

abstract class EmailClient {
  public abstract function send(
    string $address,
    string $subject,
    string $body
  ): void;
}
