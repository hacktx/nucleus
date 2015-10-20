<?hh

class ControllerConfig {
  private ?string $title;
  private Vector<(function (): bool)> $checks = Vector {};

  /**
   * Set the title for the controller
   *
   * By specifying a title with this function, when rendered, the page title
   * will contain the title specified.
   */
  public function setTitle(string $title): this {
    $this->title = $title;
    return $this;
  }

  public function getTitle(): ?string {
    return $this->title;
  }

  public function addCheck((function (): bool) $foo): this {
    $this->checks[] = $foo;
    return $this;
  }

  public function getChecks(): Vector<(function (): bool)> {
    return $this->checks;
  }
}
