<?hh

class URIMapGenerator {
  public static function getRoutesMap(): Map<string, string> {
    // Get all the php files in the cwd
    $directory = new RecursiveDirectoryIterator(getcwd() . '/routes');
    $iterator = new RecursiveIteratorIterator($directory);
    $files = new RegexIterator(
      $iterator,
      '/^.+(\.php|\.hh)$/i',
      RecursiveRegexIterator::GET_MATCH
    );
    // Get the paths from the attributes
    $path_map = Map {};
    foreach ($files as $file) {
      $paths = self::getPathsFromFile($file[0]);
      if($paths) {
        $path_map->addAll($paths->items());
      }
    }
    return $path_map;
  }

  private static function getPathsFromFile(string $file): Map<string, string> {
    $paths = Map {};
    require_once($file);
    $classes = self::getClassesFromFile($file);
    foreach ($classes as $class) {
      $controller = (new $class);
      if(method_exists($controller, 'getPath')) {
        $path = $controller->getPath();
        if($path) {
          $paths[$path] = Map {
            'controller' => $class,
            'status' => $controller->getConfig()['status'],
            'roles' => $controller->getConfig()['roles'],
          };
        }
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

$routes = URIMapGenerator::getRoutesMap();
$template =
'<?hh
/**
 * The contents of this file are auto-generated. Any changes made by hand will
 * be lost. To update the contents of this file, run "rider build" from the
 * root of your rider project.
 */

class URIMap {
  public static function getURIMap(): Map<string, string> {
    return ' . var_export($routes, true) . ';
  }
}';
file_put_contents('build/URIMap.php', $template);
