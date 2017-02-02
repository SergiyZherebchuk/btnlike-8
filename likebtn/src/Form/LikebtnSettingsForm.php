<?php
/**
 * Created by PhpStorm.
 * User: znak
 * Date: 08.01.17
 * Time: 17:17
 */

namespace Drupal\likebtn\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Entity\Entity;
use Drupal\likebtn\LikeBtn;
use Drupal\likebtn\LikebtnInterface;

class LikebtnSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return [
      'likebtn.settings'
    ];
  }

  public function getFormId() {
    return 'likebtn-settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    global $language;
    $config = $this->config('likebtn.settings');

    $form = array();

    $likebtn_website_locale = $language->language;
    $likebtn_website_locales = unserialize(LIKEBTN_WEBSITE_LOCALES);
    if (!in_array($likebtn_website_locale, $likebtn_website_locales)) {
      $likebtn_website_locale = 'en';
    }

    $likebtn_settings_lang_options['auto'] = "auto - " . t("Detect from client browser");
    $langs = unserialize(LIKEBTN_LANGS);
    foreach ($langs as $lang_code => $lang_name) {
      $likebtn_settings_lang_options[$lang_code] = $lang_name;
    }

    $likebtn_styles = $config->get('settings.likebtn_styles') ?: array();

    $likebtn_settings_style_options = array();
    if (!$likebtn_styles) {
      $likebtn_styles = unserialize(LIKEBTN_STYLES);
    }
    foreach ($likebtn_styles as $likebtn_style) {
      $likebtn_settings_style_options[$likebtn_style] = $likebtn_style;
    }

    // For assets.
    $public_url = _likebtn_public_url();

    $form['likebtn_settings_item'] = array(
      '#type'          => 'item',
      '#description'   => t('You can find detailed settings description on <a href="@link-likebtn">LikeBtn.com</a>. Options marked with tariff plan name (PLUS, PRO, VIP, ULTRA) are available only if your website is upgraded to corresponding plan (<a href="@link-read_more">read more about plans and pricing</a>).',
        array(
          '@link-likebtn'   => 'http://likebtn.com/en/#settings',
          '@link-read_more' => 'http://likebtn.com/en/#plans_pricing',
        )
      ),
    );

    $form['likebtn_extra_display_options'] = array(
      '#type'        => 'details',
      '#title'       => t('Extra display options'),
      '#open'        => FALSE,
    );

    // Settings must be under subelement to be properly flattened for field.
    $form['likebtn_extra_display_options']['likebtn_html_before'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Insert HTML before'),
      '#description'   => t('HTML code to insert before the Like Button'),
      '#default_value' => $config->get('settings.likebtn_html_before'),
    );

    $form['likebtn_extra_display_options']['likebtn_html_after'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Insert HTML after'),
      '#description'   => t('HTML code to insert after the Like Button'),
      '#default_value' => $config->get('settings.likebtn_html_after'),
    );

    $form['likebtn_extra_display_options']['likebtn_alignment'] = array(
      '#type'          => 'select',
      '#title'         => t('Alignment'),
      '#options'       => array(
        'left' => t('Left'),
        'center' => t('Center'),
        'right' => t('Right')),
      '#default_value' => $config->get('settings.likebtn_alignment'),
    );

    $form['likebtn_settings_style_language'] = array(
      '#type'        => 'details',
      '#title'       => t('Style and language'),
      '#weight'      => 4,
      '#open'        => FALSE,
    );
    $form['likebtn_settings_style_language']['likebtn_settings_style'] = array(
      '#type'          => 'select',
      '#title'         => t('Style'),
      '#description'   => 'style',
      '#options'       => $likebtn_settings_style_options,
      '#default_value' => $config->get('settings.likebtn_settings.style'),
    );
    $form['likebtn_settings_style_language']['likebtn_settings_lang'] = array(
      '#type'          => 'select',
      '#title'         => t('Language'),
      '#description'   => 'lang',
      '#default_value' => $config->get('settings.likebtn_settings.lang'),
      '#options'       => $likebtn_settings_lang_options,
    );

    $form['likebtn_settings_appearance_behaviour'] = array(
      '#type'        => 'details',
      '#title'       => t('Appearance and behaviour'),
      '#weight'      => 5,
      '#open'        => FALSE,
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_like_label'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show "like"-label'),
      '#description'   => 'show_like_label',
      '#default_value' => $config->get('settings.likebtn_settings.show_like_label'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_dislike_label'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show "dislike"-label'),
      '#description'   => 'show_dislike_label',
      '#default_value' => $config->get('settings.likebtn_settings.show_dislike_label'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_dislike'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show popup on disliking'),
      '#description'   => 'popup_dislike',
      '#default_value' => $config->get('settings.likebtn_settings.popup_dislike'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_like_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show Like Button'),
      '#description'   => 'like_enabled',
      '#default_value' => $config->get('settings.likebtn_settings.like_enabled'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_icon_like_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show like icon'),
      '#description'   => 'icon_like_show',
      '#default_value' => $config->get('settings.likebtn_settings.icon_like_show'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_icon_dislike_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show dislike icon'),
      '#description'   => 'icon_dislike_show',
      '#default_value' => $config->get('settings.likebtn_settings.icon_dislike_show'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_lazy_load'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Lazy load - if button is outside viewport it is loaded when user scrolls to it'),
      '#description'   => 'lazy_load',
      '#default_value' => $config->get('settings.likebtn_settings.lazy_load'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_dislike_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show Dislike Button'),
      '#description'   => 'dislike_enabled',
      '#default_value' => $config->get('settings.likebtn_settings.dislike_enabled'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_display_only'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Voting is disabled, display results only'),
      '#description'   => 'display_only',
      '#default_value' => $config->get('settings.likebtn_settings.display_only'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_unlike_allowed'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Allow to unlike and undislike'),
      '#description'   => 'unlike_allowed',
      '#default_value' => $config->get('settings.likebtn_settings.unlike_allowed'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_like_dislike_at_the_same_time'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Allow to like and dislike at the same time'),
      '#description'   => 'like_dislike_at_the_same_time',
      '#default_value' => $config->get('settings.likebtn_settings.like_dislike_at_the_same_time'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_revote_period'] = array(
      '#type'          => 'textfield',
      '#title'         => t('The period of time in seconds after which it is allowed to vote again'),
      '#description'   => 'revote_period',
      '#default_value' => $config->get('settings.likebtn_settings.revote_period'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_copyright'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show copyright link in the share popup') . ' (VIP, ULTRA)',
      '#description'   => 'show_copyright',
      '#default_value' => $config->get('settings.likebtn_settings.show_copyright'),
      '#states' => array(
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PRO)),
        ),
      ),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_rich_snippet'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Enable Google Rich Snippets'),
      '#description'   => t('<a href="https://likebtn.com/en/faq#rich_snippets" target="_blank">What are Google Rich Snippets and how do they boost traffic?</a>'),
      '#default_value' => $config->get('settings.likebtn_settings.rich_snippet'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_html'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Custom HTML to insert into the popup') . ' (PRO, VIP, ULTRA)',
      '#description'   => 'popup_html',
      '#default_value' => $config->get('settings.likebtn_settings.popup_html'),
      '#states' => array(
        // Enable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
        ),
      ),
    );

    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_donate'] = array(
      '#type'          => 'textfield',
      '#id'            => 'popup_donate_input',
      '#title'         => '<img src="' . $public_url . '/assets/img/popup_donate.png" width="16" height="16"/> ' . t('Donate buttons to display in the popup') . ' (VIP, ULTRA)',
      '#maxlength'     => 5000,
      '#suffix'        => '<button onclick="likebtnDG(\'popup_donate_input\'); return false;" style="position:relative;top:-10px;">' . t('Configure donate buttons') . '</button><br/><br/>',
      '#description'   => 'popup_donate',
      '#default_value' => $config->get('settings.likebtn_settings.popup_donate') ?: '',
      '#states' => array(
        // Enable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_VIP)),
        ),
      ),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_content_order'] = array(
      '#type'          => 'textfield',
      '#id'            => 'popup_content_order_input',
      '#title'         => t('Order of the content in the popup'),
      '#description'   => 'popup_content_order',
      '#default_value' => $config->get('settings.likebtn_settings.popup_content_order'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show popop after "liking" (VIP, ULTRA)'),
      '#description'   => 'popup_enabled',
      '#default_value' => $config->get('settings.likebtn_settings.popup_enabled'),
      '#states' => array(
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PRO)),
        ),
      ),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_position'] = array(
      '#type'          => 'select',
      '#title'         => t('Popup position'),
      '#description'   => 'popup_position',
      '#default_value' => $config->get('settings.likebtn_settings.popup_position'),
      '#options'       => array(
        "top"  => t('top'),
        "right" => t('right'),
        "bottom" => t('bottom'),
        "left" => t('left')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_style'] = array(
      '#type'          => 'select',
      '#title'         => t('Popup style'),
      '#description'   => 'popup_style',
      '#default_value' => $config->get('settings.likebtn_settings.popup_style'),
      '#options'       => array(
        "light"  => "light",
        "dark" => "dark"),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_hide_on_outside_click'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Hide popup when clicking outside'),
      '#description'   => 'popup_hide_on_outside_click',
      '#default_value' => $config->get('settings.likebtn_settings.popup_hide_on_outside_click'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_event_handler'] = array(
      '#type'          => 'textfield',
      '#title'         => t('JavaScript callback function serving as an event handler'),
      '#description'   => 'event_handler<br/><br/>' . t('The provided function receives the event object as its single argument. The event object has the following properties: <strong>type</strong> – indicates which event was dispatched ("likebtn.loaded", "likebtn.like", "likebtn.unlike", "likebtn.dislike", "likebtn.undislike"); <strong>settings</strong> – button settings; <strong>wrapper</strong> – button DOM-element'),
      '#default_value' => $config->get('settings.likebtn_settings.event_handler'),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_info_message'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show information message when the button can not be displayed due to misconfiguration'),
      '#description'   => 'info_message',
      '#default_value' => $config->get('settings.likebtn_settings.info_message'),
    );

    $form['likebtn_settings_counter'] = array(
      '#type'        => 'details',
      '#title'       => t('Counter'),
      '#weight'      => 6,
      '#open'        => FALSE,
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_type'] = array(
      '#type'          => 'select',
      '#title'         => t('Counter type'),
      '#description'   => 'counter_type',
      '#default_value' => $config->get('settings.likebtn_settings.counter_type'),
      '#options'       => array(
        "number"  => t('number'),
        "percent" => t('percent'),
        "substract_dislikes" => t('substract_dislikes'),
        "single_number" => t('single_number')),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_clickable'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Votes counter is clickable'),
      '#description'   => 'counter_clickable',
      '#default_value' => $config->get('settings.likebtn_settings.counter_clickable'),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show votes counter'),
      '#description'   => 'counter_show',
      '#default_value' => $config->get('settings.likebtn_settings.counter_show'),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_padding'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Counter padding'),
      '#description'   => 'counter_padding',
      '#default_value' => $config->get('settings.likebtn_settings.counter_padding'),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_zero_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show zero value in counter'),
      '#description'   => 'counter_zero_show',
      '#default_value' => $config->get('settings.likebtn_settings.counter_zero_show'),
    );

    $form['likebtn_settings_sharing'] = array(
      '#type'        => 'details',
      '#title'       => t('Sharing'),
      '#weight'      => 7,
      '#open'        => FALSE,
    );
    $form['likebtn_settings_sharing']['likebtn_settings_share_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show share buttons in the popup.') .  ' ' . t('Use popup_enabled option to enable/disable popup.') . ' (PLUS, PRO, VIP, ULTRA)',
      '#description'   => 'share_enabled',
      '#default_value' => $config->get('settings.likebtn_settings.share_enabled'),
      '#states' => array(
        // Disable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
        ),
      ),
    );
    $form['likebtn_settings_sharing']['likebtn_settings_addthis_pubid'] = array(
      '#type'          => 'textfield',
      '#title'         => t('AddThis <a href="@link-profile-id">Profile ID</a>. Allows to collect sharing statistics and view it on AddThis <a href="@link-analytics-page">analytics page</a> (PRO, VIP, ULTRA)',
        array(
          '@link-profile-id'     => 'https://www.addthis.com/settings/publisher',
          '@link-analytics-page' => 'http://www.addthis.com/analytics',
        )
      ),
      '#description'   => 'addthis_pubid',
      '#maxlength'     => 30,
      '#default_value' => $config->get('settings.likebtn_settings.addthis_pubid'),
      '#states' => array(
        // Disable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
        ),
      ),
    );
    $form['likebtn_settings_sharing']['likebtn_settings_addthis_service_codes'] = array(
      '#type'          => 'textfield',
      '#title'         => t('AddThis <a href="@link">service codes</a> separated by comma (max 8). Used to specify which buttons are displayed in share popup. Example: google_plusone_share, facebook, twitter (PRO, VIP, ULTRA)', array(
        '@link' => 'http://www.addthis.com/services/list',
      )),
      '#description'   => 'addthis_service_codes',
      '#default_value' => $config->get('settings.likebtn_settings.addthis_service_codes'),
      '#states' => array(
        // Disable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
        ),
      ),
    );

    $form['likebtn_settings_loader'] = array(
      '#type'        => 'details',
      '#title'       => t('Loader'),
      '#weight'      => 8,
      '#open'        => FALSE,
    );
    $form['likebtn_settings_loader']['likebtn_settings_loader_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show loader while button is loading'),
      '#description'   => 'loader_show',
      '#default_value' => $config->get('settings.likebtn_settings.loader_show'),
    );
    $form['likebtn_settings_loader']['likebtn_settings_loader_image'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Loader image URL (if empty, default image is used)'),
      '#description'   => 'loader_image',
      '#default_value' => $config->get('settings.likebtn_settings.loader_image'),
    );

    $form['likebtn_settings_tooltips'] = array(
      '#type'        => 'details',
      '#title'       => t('Tooltips'),
      '#weight'      => 9,
      '#open'        => FALSE
    );
    $form['likebtn_settings_tooltips']['likebtn_settings_tooltip_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show tooltips'),
      '#description'   => 'tooltip_enabled',
      '#default_value' => $config->get('settings.likebtn_settings.tooltip_enabled'),
    );

    $form['likebtn_settings_i18n'] = array(
      '#type'        => 'details',
      '#title'       => t('Labels'),
      '#weight'      => 11,
      '#open'        => FALSE,
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_like'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button label'),
      '#description'   => 'i18n_like',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_like'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_dislike'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button label'),
      '#description'   => 'i18n_dislike',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_dislike'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_after_like'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button label after liking'),
      '#description'   => 'i18n_after_like',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_after_like'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_after_dislike'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button label after disliking'),
      '#description'   => 'i18n_after_dislike',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_after_dislike'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_like_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button tooltip'),
      '#description'   => 'i18n_like_tooltip',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_like_tooltip'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_dislike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button tooltip'),
      '#description'   => 'i18n_dislike_tooltip',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_dislike_tooltip'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_unlike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button tooltip after "liking"'),
      '#description'   => 'i18n_unlike_tooltip',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_unlike_tooltip'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_undislike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button tooltip after "liking"'),
      '#description'   => 'i18n_undislike_tooltip',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_undislike_tooltip'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_share_text'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Text displayed in share popup after "liking"'),
      '#description'   => 'i18n_share_text',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_share_text'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_close'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Popup close button'),
      '#description'   => 'i18n_popup_close',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_popup_close'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_text'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Popup text when sharing is disabled'),
      '#description'   => 'i18n_popup_text',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_popup_text'),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_donate'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Text before donate buttons in the popup'),
      '#description'   => 'i18n_popup_donate',
      '#default_value' => $config->get('settings.likebtn_settings.i18n_popup_donate'),
    );

    $form['likebtn_settings_i18n']['likebtn_translate'] = array(
      '#type'          => 'item',
      '#description'   => t('<a href="https://likebtn.com/en/translate-like-button-widget" target="_blank">Send us translation</a>'),
    );

    $form['likebtn_demo_fieldset'] = array(
      '#type'        => 'details',
      '#title'       => t('Demo'),
      '#weight'      => 12,
      '#open'        => FALSE
    );

    /*

    $form['likebtn_demo_fieldset']['likebtn_demo'] = array(
      '#type'     => 'markup',
      '#markup'   => $this->likebtn_get_markup('live_demo', 1, $default_values),
    );

    */

    $form['#attached']['library'][] = 'likebtn/likebtn_libraries';

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateForm() method.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('likebtn.settings');

    $config->set('settings.likebtn_alignment', $values['likebtn_alignment'])
      ->set('settings.likebtn_settings.style', $values['likebtn_settings_style'])
      ->set('settings.likebtn_settings.lang', $values['likebtn_settings_lang'])
      ->set('settings.likebtn_settings.show_like_label', $values['likebtn_settings_show_like_label'])
      ->set('settings.likebtn_settings.show_dislike_label', $values['likebtn_settings_show_dislike_label'])
      ->set('settings.likebtn_settings.popup_dislike', $values['likebtn_settings_popup_dislike'])
      ->set('settings.likebtn_settings.like_enabled', $values['likebtn_settings_like_enabled'])
      ->set('settings.likebtn_settings.icon_like_show', $values['likebtn_settings_icon_like_show'])
      ->set('settings.likebtn_settings.icon_dislike_show', $values['likebtn_settings_icon_dislike_show'])
      ->set('settings.likebtn_settings.lazy_load', $values['likebtn_settings_lazy_load'])
      ->set('settings.likebtn_settings.dislike_enabled', $values['likebtn_settings_dislike_enabled'])
      ->set('settings.likebtn_settings.display_only', $values['likebtn_settings_display_only'])
      ->set('settings.likebtn_settings.unlike_allowed', $values['likebtn_settings_unlike_allowed'])
      ->set('settings.likebtn_settings.like_dislike_at_the_same_time', $values['likebtn_settings_like_dislike_at_the_same_time'])
      ->set('settings.likebtn_settings.show_copyright', $values['likebtn_settings_show_copyright'])
      ->set('settings.likebtn_settings.rich_snippet', $values['likebtn_settings_rich_snippet'])
      ->set('settings.likebtn_settings.popup_enabled', $values['likebtn_settings_popup_enabled'])
      ->set('settings.likebtn_settings.popup_position', $values['likebtn_settings_popup_position'])
      ->set('settings.likebtn_settings.popup_style', $values['likebtn_settings_popup_style'])
      ->set('settings.likebtn_settings.popup_hide_on_outside_click', $values['likebtn_settings_popup_hide_on_outside_click'])
      ->set('settings.likebtn_settings.event_handler', $values['likebtn_settings_event_handler'])
      ->set('settings.likebtn_settings.info_message', $values['likebtn_settings_info_message'])
      ->set('settings.likebtn_settings.counter_type', $values['likebtn_settings_counter_type'])
      ->set('settings.likebtn_settings.counter_clickable', $values['likebtn_settings_counter_clickable'])
      ->set('settings.likebtn_settings.counter_show', $values['likebtn_settings_counter_show'])
      ->set('settings.likebtn_settings.counter_padding', $values['likebtn_settings_counter_padding'])
      ->set('settings.likebtn_settings.counter_zero_show', $values['likebtn_settings_counter_zero_show'])
      ->set('settings.likebtn_settings.share_enabled', $values['likebtn_settings_share_enabled'])
      ->set('settings.likebtn_settings.addthis_pubid', $values['likebtn_settings_addthis_pubid'])
      ->set('settings.likebtn_settings.addthis_service_codes', $values['likebtn_settings_addthis_service_codes'])
      ->set('settings.likebtn_settings.loader_show', $values['likebtn_settings_loader_show'])
      ->set('settings.likebtn_settings.loader_image', $values['likebtn_settings_loader_image'])
      ->set('settings.likebtn_settings.tooltip_enabled', $values['likebtn_settings_tooltip_enabled'])
      ->set('settings.likebtn_settings.i18n_like', $values['likebtn_settings_i18n_like'])
      ->set('settings.likebtn_settings.i18n_dislike', $values['likebtn_settings_i18n_dislike'])
      ->set('settings.likebtn_settings.i18n_after_like', $values['likebtn_settings_i18n_after_like'])
      ->set('settings.likebtn_settings.i18n_after_dislike', $values['likebtn_settings_i18n_after_dislike'])
      ->set('settings.likebtn_settings.i18n_like_tooltip', $values['likebtn_settings_i18n_like_tooltip'])
      ->set('settings.likebtn_settings.i18n_dislike_tooltip', $values['likebtn_settings_i18n_dislike_tooltip'])
      ->set('settings.likebtn_settings.i18n_unlike_tooltip', $values['likebtn_settings_i18n_unlike_tooltip'])
      ->set('settings.likebtn_settings.i18n_undislike_tooltip', $values['likebtn_settings_i18n_undislike_tooltip'])
      ->set('settings.likebtn_settings.i18n_share_text', $values['likebtn_settings_i18n_share_text'])
      ->set('settings.likebtn_settings.i18n_popup_close', $values['likebtn_settings_i18n_popup_close'])
      ->set('settings.likebtn_settings.i18n_popup_text', $values['likebtn_settings_i18n_popup_text'])
      ->save();

    parent::submitForm($form, $form_state);
  }

  protected function likebtn_get_markup($element_name, $element_id, $values = NULL, $wrap = TRUE, $include_entity_data = TRUE) {
    $prepared_settings = array();
    $config = $this->config('likebtn-settings');

    $likebtn = new LikeBtn();
    $likebtn->runSyncVotes();

    $settings = unserialize(LIKEBTN_SETTINGS);

    $data = '';
    if ($element_name && $element_id) {
      $data .= 'data-identifier="' . $element_name . '_' . $element_id . '"';
    }

    $site_id = $config->get('settings.likebtn_account_data_site_id');
    if ($site_id) {
      $data .= ' data-site_id="' . $site_id . '" ';
    }

    // Website subdirectory.
    if ($config->get('settings.likebtn_settings_subdirectory')) {
      $data .= ' data-subdirectory="' . $config->get('settings.likebtn_settings_subdirectory') . '" ';
    }

    $data .= ' data-engine="drupal" ';
    if (defined('VERSION')) {
      $data .= ' data-engine_v="' . VERSION . '" ';
    }

    $data .= ' data-plugin_v="' . LikebtnInterface::LIKEBTN_VERSION . '" ';

    foreach ($settings as $option_name => $option_info) {
      if ($values) {
        if (isset($values['likebtn_settings_' . $option_name])) {
          $option_value = $values['likebtn_settings_' . $option_name];
        }
        elseif (isset($values[$option_name])) {
          $option_value = $values[$option_name];
        }
        else {
          $option_value = '';
        }
      }
      else {
        if (function_exists('variable_get_value')) {
          $option_value = $config->get->value('likebtn_settings_' . $option_name) ?: array('default' => '');
        }
        else {
          $option_value = $config->get('settings.likebtn_settings_' . $option_name);
        }
      }

      $option_value_prepared = _likebtn_prepare_option($option_name, $option_value);
      $prepared_settings[$option_name] = $option_value_prepared;

      // Do not add option if it has default value.
      if ($option_value !== '' && $option_value != $settings[$option_name]['default']) {
        $data .= ' data-' . $option_name . '="' . $option_value_prepared . '" ';
      }
    }

    // Add item options.
    if ($include_entity_data) {
      if (empty($prepared_settings['item_url']) || empty($prepared_settings['item_title'])) {
        $entity_list = array();
        $entity = NULL;
        $entity_url = '';
        $entity_title = '';
        $entity_date = '';

        // Ignore dummy entity name.
        if (\Drupal::entityTypeManager()->getDefinition($element_name)) {
          // For fields.
          $parent_entity_id = preg_replace('/_.*/', '', $element_id);
          $entity_list = Entity::load($element_name, array($parent_entity_id));
        }
        if (!empty($entity_list)) {
          $entity = array_shift($entity_list);
        }
        if ($entity && (isset($entity->title) || isset($entity->subject))) {
          // URL.
          if (empty($prepared_settings['item_url'])) {
            $entity_url_object = Entity::url($element_name, $entity);

            if (!empty($entity_url_object['path'])) {
              global $base_url;
              $entity_url = $base_url . '/' . $entity_url_object['path'];
            }
          }

          // Title.
          if (empty($prepared_settings['item_title'])) {
            if (isset($entity->title)) {
              $entity_title = $entity->title;
            }
            elseif (isset($entity->subject)) {
              $entity_title = $entity->subject;
            }
          }

          // Date.
          if (empty($prepared_settings['item_date'])) {
            if (isset($entity->created)) {
              $entity_date = date("c", $entity->created);
            }
          }
        }

        if ($entity_url) {
          $data .= ' data-item_url="' . $entity_url . '" ';
        }
        if ($entity_title) {
          $entity_title = htmlspecialchars($entity_title);
          $data .= ' data-item_title="' . $entity_title . '" ';
        }
        if ($entity_date) {
          $data .= ' data-item_date="' . $entity_date . '" ';
        }
      }
    }



    drupal_add_js("//w.likebtn.com/js/w/widget.js", array('type' => 'external', 'scope' => 'footer'));

    $widget_script = <<<WIDGET_SCRIPT
(function(d, e, s) {a = d.createElement(e);m = d.getElementsByTagName(e)[0];a.async = 1;a.src = s;m.parentNode.insertBefore(a, m)})(document, 'script', '//w.likebtn.com/js/w/widget.js'); if (typeof(LikeBtn) != "undefined") { LikeBtn.init(); }
WIDGET_SCRIPT;

    drupal_add_js($widget_script,
      array('type' => 'inline', 'scope' => 'footer')
    );

    $public_url = _likebtn_public_url();

    $markup = <<<MARKUP
<!-- LikeBtn.com BEGIN -->
<span class="likebtn-wrapper" {$data}></span>
<script type="text/javascript">if (typeof(LikeBtn) != "undefined") { LikeBtn.init(); }</script>
<!-- LikeBtn.com END -->
MARKUP;

    // HTML before.
    $html_before = '';
    if (isset($values['likebtn_html_before'])) {
      $html_before = $values['likebtn_html_before'];
    }
    else {
      $html_before = $config->get('settings.likebtn_html_before');
    }
    if (trim($html_before)) {
      $markup = $html_before . $markup;
    }

    // HTML after.
    $html_after = '';
    if (isset($values['likebtn_html_after'])) {
      $html_after = $values['likebtn_html_after'];
    }
    else {
      $html_after = $config->get('settings.likebtn_html_after');
    }
    if (trim($html_after)) {
      $markup = $markup . $html_after;
    }

    // Alignment.
    if ($wrap) {
      $alignment = '';
      if (isset($values['likebtn_alignment'])) {
        $alignment = $values['likebtn_alignment'];
      }
      else {
        $alignment = $config->get('settings.likebtn_alignment');
      }
      if ($alignment == 'right') {
        $markup = '<div style="text-align:right" class="likebtn_container">' . $markup . '</div>';
      }
      elseif ($alignment == 'center') {
        $markup = '<div style="text-align:center" class="likebtn_container">' . $markup . '</div>';
      }
      else {
        $markup = '<div class="likebtn_container">' . $markup . '</div>';
      }
    }

    return $markup;
  }

}
