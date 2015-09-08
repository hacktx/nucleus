<?hh // strict

final class :nucleus:volunteer extends :x:element {
  attribute Vector<array> volunteers;

  final protected function render(): :form {
    $volunteers = $this->getAttribute('volunteers');

    $rows = Vector {};

    foreach ($volunteers as $volunteer) {
      $rows[] =
        <tr>
          <td>
            {$volunteer['id']}
            <input
              type="hidden"
              name={$volunteer['id'].'[id]'}
              value={$volunteer['id']}
            />
          </td>
          <td>
            <input
              type="text"
              class="form-control"
              placeholder="Name"
              value={$volunteer['name']}
              name={$volunteer['id'].'[name]'}
            />
          </td>
          <td>
            <input
              type="email"
              class="form-control"
              placeholder="Email"
              value={$volunteer['email']}
              name={$volunteer['id'].'[email]'}
            />
          </td>
        </tr>;
    }

    return
      <form method="post" action={VolunteerController::getPath()}>
        <table class="table">
          <thead>
            <tr>
              <th>Volunteer ID</th>
              <th>Name</th>
              <th>Email</th>
            </tr>
          </thead>
          <tbody>
            {$rows}
            <tr>
              <td>New Volunteer</td>
              <td>
                <input
                  type="text"
                  class="form-control"
                  placeholder="Name"
                  name={'new[name]'}
                />
              </td>
              <td>
                <input
                  type="email"
                  class="form-control"
                  placeholder="Email"
                  name={'new[email]'}
                />
              </td>
            </tr>
          </tbody>
        </table>
        <button type="submit" class="btn btn-default">Save</button>
      </form>;
  }
}
