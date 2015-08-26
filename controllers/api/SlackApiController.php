<?hh

use Parse\ParseInstallation;
use Parse\ParsePush;

class SlackApiController extends BaseController {
  public static function getPath(): string {
    return '/api/slack';
  }

  public static function post(): void {
    if ($_POST['token'] !== Config::get('Slack')['token']) {
      return;
    }

    $push_data = array('alert' => $_POST['text']);
    ParsePush::send(array(
      'channels' => ["announcements"],
      'data' => $push_data
    )); 
  }
}
