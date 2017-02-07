<?php

namespace Drupal\likebtn\Plugin\Filter;

use Drupal\filter\Annotation\Filter;
use Drupal\Core\Annotation\Translation;
use Drupal\filter\Plugin\FilterBase;
use Drupal\likebtn\LikebtnInterface;
use Drupal\likebtn\LikeBtnMarkup;

/**
 * Provides a filter to limit allowed HTML tags.
 * @Filter(
 *   id = "likebtn",
 *   title = @Translation("Enable LikeBtn shortcodes"),
 *   description = @Translation("Sets up a filter that enables LikeBtn shortcodes."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_HTML_RESTRICTOR,
 *   settings = {
 *     "allowed_html" = "           ",
 *     "filter_html_help" = 1,
 *     "filter_html_nofollow" = 0
 *   },
 *   weight = 0
 * )
 */
class LikeBtnFilter extends FilterBase {
  public function process($text, $langcode) {
    $markup_render = new LikeBtnMarkup();

    $replacements = array();
    $regex = '/(?<!\<code\>)\[' . LikebtnInterface::LIKEBTN_SHORTCODE . '([^}\n]*?)\](?!\<\/code\>)/is';
    preg_match_all($regex, $text, $matches);

    if (!empty($matches[1])) {
      // Parse options.
      foreach ($matches[1] as $index => $params_str) {
        $regex_list[$index] = $regex;
        $replacements[$index] = '';
        $regex_params = '/(\w+)\s*=\s*\"(.*?)\"/si';
        preg_match_all($regex_params, $params_str, $matches_params);

        if (!count($matches_params)) {
          continue;
        }

        $settings = array();
        foreach ($matches_params[1] as $matches_params_index => $option) {
          $settings[$option] = _likebtn_prepare_option($option, $matches_params[2][$matches_params_index]);
        }

        $markup = $markup_render->likebtn_get_markup('', '', $settings, FALSE, FALSE);
        $replacements[$index] = $markup;
      }
      $text = preg_replace($regex_list, $replacements, $text, 1);
    }
    return $text;
  }

  public function prepare($text, $langcode) {
    return $text;
  }

  public function tips($long = FALSE) {
    return '[likebtn identifier="my_button_in_post" style="large" i18n_like="Yeah!"] - ' . $this->t('Insert a Like Button using shortcode.');
  }
}
