<?php

namespace Drupal\likebtn\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'isbn' widget.
 *
 * @FieldWidget(
 *   id = "likebtn_default_widget",
 *   label = ("Like Button"),
 *   field_types = {
 *     "likebtn_field"
 *   }
 * )
 */
class LikebtnFieldWidget extends WidgetBase {
   public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
     //
   }
}
