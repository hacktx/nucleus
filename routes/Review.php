<?hh

class Review {
  public static function get(): :xhp {
    parse_str($_SERVER['QUERY_STRING'], $query_params);
    var_dump($query_params);

    return
      <h1>Body</h1>;
  }
}
