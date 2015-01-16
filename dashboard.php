<?php

require_once('lib/OmegaCore.php');

if(!Session::isActive()) {
  header('Location: index.php');
}

# Redirect applicants to application page
$user = Session:getUser();
if (!$user->isMember()) {
  header('Location: apply.php');
}

require_once('templates/header.html');
require_once('templates/footer.html');
