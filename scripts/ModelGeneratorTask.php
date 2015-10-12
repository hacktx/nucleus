<?hh

trait ModelGeneratorTrait {
  public function taskGenModels() {
    return new ModelGeneratorTask();
  }
}

class ModelGeneratorTask extends Robo\Task\BaseTask
  implements Robo\Contract\TaskInterface {
  public function run(): Robo\Result {
    $this->printTaskInfo('Generating Models');
    self::generate();
    return Robo\Result::success($this, "Finished Generating Models");
  }

  private static function generate(): void {
    // Get all the php files in the cwd
    $directory = new RecursiveDirectoryIterator(getcwd().'/models/schema');
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator(
      $iterator,
      '/^.+(\.php|\.hh)$/i',
      RegexIterator::MATCH,
      RegexIterator::USE_KEY,
    );

    foreach ($files as $file) {
      $classes = self::getClassesFromFile($file);
      foreach ($classes as $class) {
        $controller = new $class();
        if ($controller instanceof ModelSchema) {
          (new ModelGenerator($controller))->generate(); 
          (new ModelMutatorGenerator($controller))->generate(); 
        }
      }
    }
  }

  private static function getClassesFromFile(string $file): Vector<string> {
    $classes = Vector {};
    $tokens = token_get_all(file_get_contents($file));
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
      if ($tokens[$i - 2][0] == T_CLASS &&
          $tokens[$i - 1][0] == T_WHITESPACE &&
          $tokens[$i][0] == T_STRING) {
        $class_name = $tokens[$i][1];
        $classes[] = $class_name;
      }
    }
    return $classes;
  }
}
