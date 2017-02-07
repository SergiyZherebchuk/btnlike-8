<?php

/**
 * @file
 * Contains Drupal\likebtn\Plugin\Field\FieldWidget\LikebtnFieldWidget.
 */

namespace Drupal\likebtn\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Field\WidgetInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'isbn' widget.
 *
 * @FieldWidget(
 *   id = "likebtn_default_widget",
 *   module = "likebtn",
 *   label = @Translation("Like Button"),
 *   field_types = {
 *     "likebtn_field"
 *   }
 * )
 */
class LikebtnFieldWidget extends WidgetBase implements WidgetInterface {
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
		//
  }
}
