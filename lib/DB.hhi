<?hh // decl

class DB {
  public static string $user;
  public static string $password;
  public static string $dbName;
  public static string $port;

  public static function query(string $query, ...): array;
  public static function queryFirstRow(string $query, ...): array;
  public static function insert(string $table, array<string, mixed> $params): void;
  public static function insertUpdate(string $table, array<string, mixed> $params): void;
  public static function update(string $table, array<string, mixed> $params, string $format, ...): void;
  public static function delete(string $table, string $format, ...): void;
  public static function count(): int;
  public static function insertId(): int;
}