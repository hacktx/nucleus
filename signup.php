<?php

require_once('lib/OmegaCore.php');

if(Session::isActive()) {
  $user = Session::getUser();
  if($user->isMember()) {
    header('Location: dashboard.php');
  } else {
    header('Location: apply.php');
  }
}

require_once('templates/header.html');
echo '
  <body>
    <form method="POST" action="signup.php">
      <input type="text" placeholder="Username" />
      <input type="password" placeholder="Password" />
      <input type="password" placeholder="Confirm password" />
      <input type="text" placeholder="First Name" />
      <input type="text" placeholder="Last Name" />
      <button type="submit">Submit</button>
    </form>
  </body>  
';
require_once('templates/footer.html');
