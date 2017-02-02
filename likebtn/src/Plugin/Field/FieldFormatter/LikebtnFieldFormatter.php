<?php

namespace Drupal\likebtn\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormStateInterface;

/**
 * Plugin implementation of the 'example_formatter' formatter
 *
 * @FieldFormatter(
 *   id = "default",
 *   label = @Translation("LikeBtn (default)"),
 *   field_types = {
 *     "likebtn_field"
 *   },
 * )
 */
class LikeBtnFieldFormatter extends FormatterBase {

	/**
	 * Builds a renderable array for a field value.
	 *
	 * @param \Drupal\Core\Field\FieldItemListInterface $items
	 *   The field values to be rendered.
	 * @param string $langcode
	 *   The language that should be used to render the field.
	 *
	 * @return array
	 *   A renderable array for $items, as an array of child elements keyed by
	 *   consecutive numeric indexes starting from 0.
	 */
	public function viewElements(FieldItemListInterface $items, $langcode) {
		$entity_info = entity_get_info($entity_type);
		$entity_id_key = $entity_info['entity keys']['id'];

		$elements = array();
		foreach ($items as $delta => $item) {
			$elements[$delta] = array(
				'#markup' => _likebtn_get_markup($entity_type, $entity->$entity_id_key . '_field_' . $instance['field_id'] . '_index_' . $delta, _likebtn_flatten_field_instance_settings($instance['settings'])),
			);
		}

		return $elements;
	}
}
