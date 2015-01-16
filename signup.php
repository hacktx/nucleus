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

# Deal with a submitted form
if($_SERVER['REQUEST_METHOD'] === "POST") {
  $user = User::create(
    $_POST['uname'],
    $_POST['password'],
    $_POST['email'],
    $_POST['fname'],
    $_POST['lname']
  );
  Session::create($user);
  header('Location: apply.php');
}

require_once('templates/header.html');
echo '
  <body>
    <form method="POST" action="signup.php">
      <input type="text" name="uname" placeholder="Username" />
      <input type="password" name="password" placeholder="Password" />
      <input type="password" name="password2" placeholder="Confirm password" />
      <input type="email" name="email" placeholder="email" />
      <input type="text" name="fname" placeholder="First Name" />
      <input type="text" name="lname" placeholder="Last Name" />
      <button type="submit">Submit</button>
    </form>
  </body>
';
require_once('templates/footer.html');
