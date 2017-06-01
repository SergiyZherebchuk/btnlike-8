<?php

/**
 * @file
 * Contains \Drupal\likebtn\Plugin\Field\FieldType\LikebtnFieldItem.
 */

namespace Drupal\likebtn\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\Form\FormStateInterface;
use Drupal\likebtn\Controller\LikeBtnController;

/**
 * @FieldType(
 *   id = "likebtn_field",
 *   label = @Translation("LikeBtn"),
 *   module = "likebtn",
 *   description = @Translation("Like Button."),
 *   category = @Translation("Custom"),
 *   default_widget = "likebtn_default_widget",
 *   default_formatter = "default"
 * )
 */
class LikebtnFieldItem extends FieldItemBase implements FieldItemInterface {

  /**
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_definition
   *
   * @return mixed
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['likebtn_likes'] = DataDefinition::create('integer')->setDescription(t('Likes count'));
    $properties['likebtn_dislikes'] = DataDefinition::create('integer')->setDescription(t('Dislikes count'));
    $properties['likebtn_likes_minus_dislikes'] = DataDefinition::create('integer')->setDescription(t('Likes minus dislikes'));

    return $properties;
  }

  /**
   * @param \Drupal\Core\Field\FieldStorageDefinitionInterface $field_definition
   *
   * @return array
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return array(
      'columns' => array(
        'likebtn_likes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
        ),
        'likebtn_dislikes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
        ),
        'likebtn_likes_minus_dislikes' => array(
          'type'        => 'int',
          'size'        => 'normal',
          'not null'    => FALSE,
          'sortable'    => TRUE,
          'default'     => 0,
        ),
      ),
    );
  }

  public function isEmpty() {
    $field = LikebtnFieldItem::getSettings();
    foreach ($field as $field_name => $dummy) {
      if (!empty($item[$field_name])) {
        return FALSE;
      }
    }
    return TRUE;
  }

  public static function defaultFieldSettings() {
    $fieldSetting = '';
    $settings = unserialize(LIKEBTN_SETTINGS);
    foreach ($settings as $option_name => $option_info) {
      $fieldSetting['likebtn_field']['settings'][$option_name] = $option_info['default'];
      $fieldSetting['likebtn_field']['instance_settings'][$option_name] = $option_info['default'];
    }

    return $fieldSetting;
  }

  public function fieldSettingsForm(array $form, FormStateInterface $form_state) {
    $controller = new LikeBtnController();
    $form = $controller->likebtn_settings_form($controller->likebtn_flatten_field_instance_settings(LikebtnFieldItem::getSettings()));

    return $form;
  }
}
