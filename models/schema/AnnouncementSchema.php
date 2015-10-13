<?hh // strict

class AnnouncementSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'ID' => ModelField::int_field('id'),
      'Text' => ModelField::string_field('text'),
      'Timestamp' => ModelField::date_field('timestamp'),
    };
  }

  public function getTableName(): string {
    return 'announcement';
  }

  public function getIdField(): string {
    return 'id';
  }
}
