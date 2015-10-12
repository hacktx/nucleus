<?hh // strict

namespace Facebook\HackCodegen;

/**
 * For a given ModelSchema, this class generates code for a class
 * that will allow to read the data from a database and store it
 * in the object.
 */
class ModelGenerator {

  public function __construct(
    private \ModelSchema $schema,
  ) {}

  private function getSchemaName(): string {
    $ref = new \ReflectionClass($this->schema);
    $name = $ref->getShortName();
    return Str::endsWith($name, 'Schema')
      ? Str::substr($name, 0, -6)
      : $name;
  }

  public function generate(): void {
    $class = codegen_class($this->getSchemaName())
      ->setIsFinal()
      ->setConstructor($this->getConstructor())
      ->addMethod($this->getLoad())
      ->addMethods($this->getGetters());

    codegen_file(dirname(__FILE__).'/../models/'.$this->getSchemaName().'.php')
      ->addClass($class)
      ->save();
  }

  private function getConstructor(): CodegenConstructor {
    return codegen_constructor()
      ->setPrivate()
      ->addParameter('private Map<string, mixed> $data');
  }

  private function getLoad(): CodegenMethod {
    $sql = 'select * from '.
      $this->schema->getTableName().
      ' where '.$this->schema->getIdField().'=$id';

    $body = hack_builder()
      ->addLine('$result = DB::query("'.$sql.'");')
      ->startIfBlock('!$result')
      ->addReturn('null')
      ->endIfBlock()
      ->addReturn('new %s(new Map($result))', $this->getSchemaName());

    return codegen_method('load')
      ->setIsStatic()
      ->addParameter('int $id')
      ->setReturnType('?'.$this->getSchemaName())
      ->setBody($body->getCode());
  }

  private function getGetters(): Vector<CodegenMethod> {
    $methods = Vector {};
    foreach ($this->schema->getFields() as $name => $field) {
      $return_type = $field->getType();
      $data = '$this->data[\''.$field->getDbColumn().'\']';
      $return_data = $data;
      if ($return_type == 'DateTime') {
        $return_data = 'new DateTime('.$data.')';
      } else {
        $return_data = "($return_type) $data";
      }
      if ($field->isOptional()) {
        $return_type = '?'.$return_type;
        $builder = hack_builder();
        if ($field->isManual()) {
          $builder->beginManualSection($name);
        }
        $builder->addWithSuggestedLineBreaks(
          "return isset($data)\t? $return_data\t: null;",
        );
        if ($field->isManual()) {
          $builder->endManualSection();
        }
        $body = $builder->getCode();
      } else {
        $body = 'return '.$return_data.';';
      }
      $methods[] = codegen_method('get'.$name)
        ->setReturnType($return_type)
        ->setBody($body);
    }
    return $methods;
  }
}
