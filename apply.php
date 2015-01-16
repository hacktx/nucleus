<?php

require_once('lib/OmegaCore.php');

# You must be authed to view the application
if(!Session::isActive()) {
   header('Location: login.php');
}

# Members have nothing to do here
$user = Session::getUser();
if($user->isMember()) {
  header('Location: dashboard.php');
}

$application = Application::genByUser($user);

require_once('templates/header.html');
echo '
  <body>
    <form method="POST" action="apply.php">
    </form>
  </body>
';
require_once('templates/footer.html');
