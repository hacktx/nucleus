<?php

require_once('lib/OmegaCore.php');

# If a user is logged in, redirect them to where they belong
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
  <div id="crest"></div>
  <div id="login">
    <button id="signin" href="login">Login</button>
    <button id="signup">Sign Up</button>
  </div>
</body>
';
require_once('templates/footer.html');
