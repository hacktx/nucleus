<?hh

class Login {
  public static function get(): :body {
    return
      <body>
        <form method="post" action="/login">
          <input type="text" name="username" placeholder="Username" />
          <input type="password" name="password" placeholder="Password" />
        </form>
      </body>;
  }
}
