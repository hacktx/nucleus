<?hh // strict

class UserSchema implements ModelSchema {
  public function getFields(): Map<string, ModelField> {
    return Map {
      'ID' => ModelField::int_field('id'),
      'Email' => ModelField::string_field('email'),
      'FirstName' => ModelField::string_field('fname'),
      'LastName' => ModelField::string_field('lname'),
      'Graduation' => ModelField::string_field('graduation'),
      'Major' => ModelField::string_field('major'),
      'ShirtSize' => ModelField::string_field('shirt_size'),
      'DietaryRestrictions' => ModelField::string_field('dietary_restrictions'),
      'SpecialNeeds' => ModelField::string_field('special_needs'),
      'Birthday'  => ModelField::date_field('birthday'),
      'Gender' => ModelField::string_field('gender'),
      'PhoneNumber' => ModelField::string_field('phone_number'),
      'School' => ModelField::string_field('school'),
      'Status' => ModelField::int_field('status'),
      'PersonalWebsite' => ModelField::string_field('personal_website')->optional(),
    };
  }

  public function getTableName(): string {
    return 'users';
  }

  public function getIdField(): string {
    return 'id';
  }
}
