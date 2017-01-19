<?php


/**
 * Plugin implementation of the 'country' field type.
 *
 * @FieldType(
 *   id = "likebtn_field",
 *   label = @Translation("LikeBtn"),
 *   description = @Translation("Like Button."),
 *   category = @Translation("Custom"),
 *   default_widget = "likebtn_default_widget",
 *   default_formatter = "default"
 * )
 */
namespace Drupal\likebtn\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

class LikebtnFieldItem extends FieldItemBase {
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    // TODO: Implement propertyDefinitions() method.
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    // TODO: Implement schema() method.
  }
}
