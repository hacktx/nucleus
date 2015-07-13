<?hh

final class :nucleus:nav-buttons extends :x:element {
  attribute
    User user,
    string controller;

  final protected function render(): :ul {
    $user = $this->getAttribute('user');
    $controller = $this->getAttribute('controller');

    $nav_buttons =
      <ul class="nav navbar-nav">
        <li class={$controller === 'DashboardController' ? 'active' : ''}>
          <a href="/dashboard">Dashboard</a>
        </li>
      </ul>;

    # Applicants can see the application portal
    if($user->isApplicant()) {
      $nav_buttons->appendChild(
        <li class={$controller === 'ApplyController' ? 'active' : ''}>
          <a href="/apply">Apply</a>
        </li>
      );
    }

    # Admins and Reviewers can access the review portal
    if($user->isAdmin() || $user->isReviewer()) {
      $nav_buttons->appendChild(
        <li class={($controller === 'ReviewListController' || $controller === 'ReviewSingleController') ? 'active' : ''}>
          <a href="/review">Review</a>
        </li>
      );
    }

    # Admins and event admins can access the events portal
    if($user->isAdmin() || $user->isOfficer()) {
      $nav_buttons->appendChild(
        <li class={$controller === 'EventsAdminController' ? 'active' : ''}>
          <a href="/events/admin">Events</a>
        </li>
      );
      $nav_buttons->appendChild(
        <li class={$controller === 'NotifyController' ? 'active' : ''}>
          <a href="/notify">Send Notification</a>
        </li>
      );
    }

    # Admin only actions
    if($user->isAdmin()) {
      $nav_buttons->appendChild(
        <li class={$controller === 'MembersController' ? 'active' : ''}>
          <a href="/members">Members</a>
        </li>
      );
    }

    return $nav_buttons;
  }
}
