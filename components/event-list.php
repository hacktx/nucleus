<?hh

final class :omega:event-list extends :x:element {
  final protected function render(): ?:xhp {
    $events = Event::getAll();

    if(empty($events)) {
      return null;
    }

    $event_list =
      <table class="table">
        <tr>
          <th>Name</th>
          <th>Location</th>
          <th>When</th>
        </tr>
      </table>;

    foreach($events as $event) {
      $timestamp = strtotime($event['datetime']);
      $event_list->appendChild(
        <tr>
          <td>{$event['name']}</td>
          <td>{$event['location']}</td>
          <td>{date('n/j/Y \@ g:i A', $timestamp)}</td>
        </tr>
      );
    }

    return $event_list;
  }
}
