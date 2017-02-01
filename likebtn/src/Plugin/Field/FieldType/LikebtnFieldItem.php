<?php

namespace Drupal\likebtn\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

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
class LikebtnFieldItem extends FieldItemBase {
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['likebtn_likes'] = DataDefinition::create('likebtn_likes')->setLabel(t('Likes count'));
    $properties['likebtn_dislikes'] = DataDefinition::create('likebtn_dislikes')->setLabel(t('Dislikes count'));
    $properties['likebtn_likes_minus_dislikes'] = DataDefinition::create('likebtn_likes_minus_dislikes')->setLabel(t('Likes minus dislikes'));

    return $properties;
  }

  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'likebtn_likes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
          'description' => 'Likes count',
        ),
        'likebtn_dislikes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
          'description' => 'Dislikes count',
        ),
        'likebtn_likes_minus_dislikes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
          'description' => 'Likes minus dislikes',
        ),
      ),
    );
  }

  public function isEmpty() {
    $field = $this->get('likebtn_field');
    foreach ($field['settings'] as $field_name => $dummy) {
      if (!empty($item[$field_name])) {
        return FALSE;
      }
    }
    return TRUE;
  }

  public static function defaultFieldSettings() {
    $settings = unserialize(LIKEBTN_SETTINGS);
    foreach ($settings as $option_name => $option_info) {
      $info['likebtn_field']['settings'][$option_name] = $option_info['default'];
      $info['likebtn_field']['instance_settings'][$option_name] = $option_info['default'];
    }

    return $settings;
  }
}
