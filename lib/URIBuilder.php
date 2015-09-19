<?hh // strict

class URIBuilder {
  private string $base_path = "";
  private Map<string, string> $params = Map {};

  public function __construct(string $base_path): void {
    $this->base_path = $base_path;
  }

  public function setParam(string $name, mixed $value): this {
    $this->params[$name] = (string) $value;
    return $this;
  }

  public function getURI(): string {
    return $this->base_path.'?'.http_build_query($this->params);
  }
}
