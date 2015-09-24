<?hh // strict

abstract class BaseController {
  abstract public static function getPath(): string;

  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig());
  }

  public static function getPathParam(string $key): string {
    return getRouteParams()[$key];
  }
}
