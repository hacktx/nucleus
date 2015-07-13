<?hh // strict

abstract class BaseController {
  abstract public static function getPath(): string;
  abstract public static function getConfig(): ControllerConfig;
}
