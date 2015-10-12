<?hh // strict

interface ModelSchema {
  public function getFields(): Map<string, ModelField>;
  public function getTableName(): string;
  public function getIdField(): string;
}
