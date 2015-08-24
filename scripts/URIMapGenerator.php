<?hh

trait URIGenerator {
  public function taskGenURIMap() {
    return new URIMapGenerator();
  }
}

class URIMapGenerator extends Robo\Task\BaseTask implements Robo\Contract\TaskInterface {
  public function run(): Robo\Result {
    $this->printTaskInfo('Generating URI Map');
    $routes = URIMapGenerator::getRoutesMap();
    $map = var_export($routes, true);
    $template = "<?hh\nreturn " . str_replace("HH\\", "", $map) . ";";
    file_put_contents('build/URIMap.php', $template);
    $this->printTaskSuccess("Finished Generating URI Map");
    return Robo\Result::success($this, "Finished Generating URI Map");
  }

  private static function getRoutesMap(): Map<string, string> {
    // Get all the php files in the cwd
    $directory = new RecursiveDirectoryIterator(getcwd() . '/controllers');
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator(
      $iterator,
      '/^.+(\.php|\.hh)$/i',
      RecursiveRegexIterator::GET_MATCH
    );
    // Get the paths from the attributes
    $path_map = Map {};
    foreach ($files as $file) {
      $paths = self::getPathsFromFile(((array)$file)[0]);;
      if($paths) {
        $path_map->addAll($paths->items());
      }
    }
    return $path_map;
  }

  private static function getPathsFromFile(string $file): Map<string, string> {
    $paths = Map {};
    $classes = self::getClassesFromFile($file);
    foreach ($classes as $class) {
      $controller = new $class();
      if($controller instanceof BaseController) {
        $paths[$controller->getPath()] = $class;
      }
    }
    return $paths;
  }

  private static function getClassesFromFile(string $file): Vector<string> {
    $classes = Vector {};
    $tokens = token_get_all(file_get_contents($file));
    $count = count($tokens);
    for ($i = 2; $i < $count; $i++) {
      if ($tokens[$i - 2][0] == T_CLASS &&
          $tokens[$i - 1][0] == T_WHITESPACE &&
          $tokens[$i][0] == T_STRING
      ) {
          $class_name = $tokens[$i][1];
          $classes[] = $class_name;
      }
    }
    return $classes;
  }
}
