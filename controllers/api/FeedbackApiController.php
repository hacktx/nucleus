<?hh

class FeedbackApiController extends BaseController {
  public static function getPath(): string {
    return '/api/feedback';
  }

  public static function post(): void {
    if (!isset($_POST['id']) || !isset($_POST['rating'])) {
      http_response_code(400);
      return;
    }

    $data = Map {'event_id' => $_POST['id'], 'rating' => $_POST['rating']};

    DB::insert('feedback', $data);

    $client = KeenIO\Client\KeenIOClient::factory(
      [
        'projectId' => Config::get('Keen')['project_id'],
        'writeKey' => Config::get('Keen')['write_key'],
        'readKey' => Config::get('Keen')['read_key'],
      ],
    );
    $client->addEvent('event_feedback', $data->toArray());
  }
}
