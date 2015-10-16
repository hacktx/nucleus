<?hh // strict

abstract class BaseController {
  /**
   * The path which the controller will live on
   *
   * A controller can specify the path it is to live on by specifying which in
   * this method. A path can be basic, such as `/example`, or use regex to
   * specify path params, such as `/example/(?<id>\d+)`, which can be retreived
   * using `getPathParam()`
   */
  abstract public static function getPath(): string;

  /**
   * The configration for the controller.
   *
   * Controller configs can be used to setup permissions for a controller, or
   * additional options such as the title of the page.
   */
  public static function getConfig(): ControllerConfig {
    return (new ControllerConfig());
  }

  /**
   * Get a path param for a controller which uses a regex path
   *
   * When a controller specifies a regex-based path with a path param, such as
   * `/example/(?<id>\d+)`, this function will return the value in the path
   * with the key `id`.
   */
  public static function getPathParam(string $key): string {
    return getRouteParams()[$key];
  }
}
