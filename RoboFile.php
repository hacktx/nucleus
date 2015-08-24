<?php

require 'vendor/autoload.php';
require 'scripts/URIMapGenerator.php';

class RoboFile extends \Robo\Tasks {
  use URIGenerator;

  function build() {
    $this->taskGenURIMap()->run();
  }
}
