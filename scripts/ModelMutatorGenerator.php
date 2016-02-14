<?hh // strict

namespace Facebook\HackCodegen;

/**
 * For a given ModelSchema, this class generates code for a class
 * that will allow to insert rows in a database.
 */
class ModelMutatorGenerator {

  public function __construct(
    private \ModelSchema $schema,
  ) {}

  private function getName(): string {
    $ref = new \ReflectionClass($this->schema);
    $name = $ref->getShortName();
    $remove_schema = Str::endsWith($name, 'Schema')
      ? Str::substr($name, 0, -6)
      : $name;
    return $remove_schema.'Mutator';
  }

  public function generate(): void {
    $name = $this->getName();

    $class = codegen_class($name)
      ->setIsFinal()
      ->addVar($this->getDataVar())
      ->setConstructor($this->getConstructor())
      ->addMethod($this->getCreateMethod())
      ->addMethod($this->getUpdateMethod())
      ->addMethod($this->getDeleteMethod())
      ->addMethod($this->getSaveMethod())
      ->addMethod($this->getCheckRequiredFieldsMethod())
      ->addMethods($this->getSetters())
      ->setHasManualMethodSection();

    codegen_file(dirname(__FILE__).'/../models/'.$name.'.php')
      ->addClass($class)
      ->setIsStrict(true)
      ->save();
  }


  private function getDataVar(): CodegenMemberVar {
    return codegen_member_var('data')
      ->setType('Map<string, mixed>')
      ->setValue(Map {});
  }

  private function getConstructor(): CodegenConstructor {
    return codegen_constructor()
      ->addParameter('private ?int $id = null')
      ->setPrivate();
  }

  private function getCreateMethod(): CodegenMethod {
    return codegen_method('create')
      ->setReturnType('this')
      ->setIsStatic()
      ->setBody(
        hack_builder()
        ->addReturn('new %s()', $this->getName())
        ->getCode()
      );
  }

  private function getUpdateMethod(): CodegenMethod {
    return codegen_method('update')
      ->addParameter('int $id')
      ->setReturnType('this')
      ->setIsStatic()
      ->setBody(
        hack_builder()
        ->addReturn('new %s($id)', $this->getName())
        ->getCode()
      );
  }

  private function getDeleteMethod(): CodegenMethod {
    return codegen_method('delete')
      ->addParameter('int $id')
      ->setReturnType('void')
      ->setIsStatic()
      ->setBody(
        hack_builder()
        ->addLine(
          'DB::delete("%s", "%s=%%s", $id);', 
          $this->schema->getTableName(),
          $this->schema->getIdField()
        )
        ->getCode()
      );
  }

  private function getSaveMethod(): CodegenMethod {
    $body = hack_builder()
      ->addAssignment('$id', '$this->id')
      ->startIfBlock('$id === null')
      ->addLine('$this->checkRequiredFields();')
      ->addLine(
        'DB::insert("%s", $this->data->toArray());',
        $this->schema->getTableName()
      )
      ->addReturn('(int) DB::insertId()')
      ->addElseBlock()
      ->addLine(
        'DB::update("%s", $this->data->toArray(), "%s=%%s", $this->id);',
        $this->schema->getTableName(),
        $this->schema->getIdField()
      )
      ->addReturn('$id')
      ->endIfBlock();

    return codegen_method('save')
      ->setReturnType('int')
      ->setBody($body->getCode());
  }

  private function getCheckRequiredFieldsMethod(): CodegenMethod {
    $required = $this->schema->getFields()
      ->filter($field ==> !$field->isOptional())
      ->map($field ==> $field->getDbColumn())
      ->values()->toSet();

    $body = hack_builder()
      ->add('$required = ')
      ->addSet($required)
      ->closeStatement()
      ->addAssignment(
        '$missing',
        '$required->removeAll($this->data->keys());'
      )
      ->addMultilineCall(
        'invariant',
        Vector {
          '$missing->isEmpty()',
          '"The following required fields are missing: "'.
            '.implode(", ", $missing)',
        }
      );

    return codegen_method('checkRequiredFields')
      ->setReturnType('void')
      ->setBody($body->getCode());
  }

  private function getSetters(): Vector<CodegenMethod> {
    $methods = Vector {};
    foreach($this->schema->getFields() as $name => $field) {
      if ($field->getType() === 'DateTime') {
        $value = '$value->format("Y-m-d")';
      } else {
        $value = '$value';
      }

      $body = hack_builder();
      if ($field->isManual()) {
        $body->beginManualSection($name);
      }

      $body
        ->addLine('$this->data["%s"] = %s;', $field->getDbColumn(), $value);

      if ($field->isManual()) {
        $body->endManualSection();
      }

      $body->addReturn('$this');

      $methods[] = codegen_method('set'.$name)
        ->setReturnType('this')
        ->addParameter($field->getType().' $value')
        ->setBody($body->getCode());
    }
    return $methods;
  }
}
