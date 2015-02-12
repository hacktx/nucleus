<?hh

final class :omega:event-list extends :x:element {
  final protected function render(): ?:xhp {
    $events = Event::genAllFuture();

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
      $event_list->appendChild(
        <tr>
          <td>{$event->getName()}</td>
          <td>{$event->getLocation()}</td>
          <td>{$event->getDatetime()}</td>
        </tr>
      );
    }

    return $event_list;
  }
}
