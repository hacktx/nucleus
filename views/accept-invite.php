<?hh // strict

final class :nucleus:accept-invite extends :x:element {
  attribute User user;

  final protected function render(): :div {
    return
      <div class="col-md-6 col-md-offset-3 text-center">
        <form method="post" action={AcceptInviteController::getPath()}>
          <button type="submit" class="btn btn-success" name="accept">Accept my invite!</button>
        </form> 
        <form method="post" action={AcceptInviteController::getPath()}>
          <button type="submit" class="btn btn-default" name="deny">I can no longer attend</button>
        </form> 
      </div>;
  }
}
