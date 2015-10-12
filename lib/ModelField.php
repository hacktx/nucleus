<?hh // strict

class ModelField {
  private bool $optional = false;
  private bool $manual = false;

  public function __construct(
    private string $dbColumn,
    private string $type,
  ) {}

  public function getDbColumn(): string {
    return $this->dbColumn;
  }

  public function getType(): string {
    return $this->type;
  }

  public function optional(): this {
    $this->optional = true;
    return $this;
  }

  public function isOptional(): bool {
    return $this->optional;
  }

  public function manual(): this {
    $this->manual = true;
    return $this;
  }

  public function isManual(): bool {
    return $this->manual;
  }

  public static function string_field(string $name): ModelField {
    return new ModelField($name, 'string');
  }

  public static function date_field(string $name): ModelField {
    return new ModelField($name, 'DateTime');
  }

  public static function int_field(string $name): ModelField {
    return new ModelField($name, 'int');
  }

  public static function bool_field(string $name): ModelField {
    return new ModelField($name, 'bool');
  }
}
