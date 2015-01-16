<?php

class AutoLoader {
  public static function loadFile($class) {
    require_once('lib/' . $class . '.php');
  }
}
