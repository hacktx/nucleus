<?php

require_once('lib/OmegaCore.php');

if(Session::isActive()) {
  $user = Session::getUser();
  if($user->isMember()) {
    http_redirect('dashboard');
  } else {
    http_redirect('apply');
  }
}

require_once('templates/header.html');
echo "
  <body>
  </body>
";
require_once('templates/footer.html');
