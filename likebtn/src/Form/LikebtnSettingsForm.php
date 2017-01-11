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
use Drupal\node\Entity\NodeType;
use Drupal\Core\Url;

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
    $config = $this->config('likebtn-settings');

    $form = array();

    $likebtn_website_locale = $language->language;
    $likebtn_website_locales = unserialize(LIKEBTN_WEBSITE_LOCALES);
    if (!in_array($likebtn_website_locale, $likebtn_website_locales)) {
      $likebtn_website_locale = 'en';
    }

    $form['#attached']['js'][] = array(
      'type' => 'file',
      'data' => '//likebtn.com/' . $likebtn_website_locale . '/js/donate_generator.js', array('type' => 'external', 'scope' => 'footer')
    );


    $likebtn_settings_lang_options['auto'] = "auto - " . t("Detect from client browser");
    $langs = unserialize(LIKEBTN_LANGS);
    foreach ($langs as $lang_code => $lang_name) {
      $likebtn_settings_lang_options[$lang_code] = $lang_name;
    }
    //}

    // Get styles.
    $likebtn_styles = $config->get('settings.likebtn_styles') ?: array();

    $likebtn_settings_style_options = array();
    if (!$likebtn_styles) {
      // Styles have not been loaded using API yet, load default languages.
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
      '#type'        => 'fieldset',
      '#title'       => t('Extra display options'),
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );

    // Settings must be under subelement to be properly flattened for field.
    $form['likebtn_extra_display_options']['likebtn_html_before'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Insert HTML before'),
      '#description'   => t('HTML code to insert before the Like Button'),
      '#default_value' => ($default_values ? (isset($default_values['likebtn_html_before']) ?
        $default_values['likebtn_html_before'] : '') : $config->get('settings.likebtn_html_before') ?: ''),
    );

    $form['likebtn_extra_display_options']['likebtn_html_after'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Insert HTML after'),
      '#description'   => t('HTML code to insert after the Like Button'),
      '#default_value' => ($default_values ? (isset($default_values['likebtn_html_after']) ?
        $default_values['likebtn_html_after'] : '') : $config->get('settings.likebtn_html_after') ?: ''),
    );

    $form['likebtn_extra_display_options']['likebtn_alignment'] = array(
      '#type'          => 'select',
      '#title'         => t('Alignment'),
      '#options'       => array(
        'left' => t('Left'),
        'center' => t('Center'),
        'right' => t('Right')),
      '#default_value' => ($default_values ? (isset($default_values['likebtn_alignment']) ?
        $default_values['likebtn_alignment'] : 'left') : $config->get('settings.likebtn_alignment') ?: 'left'),
    );

    $form['likebtn_settings_style_language'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Style and language'),
      '#weight'      => 4,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_style_language']['likebtn_settings_style'] = array(
      '#type'          => 'select',
      '#title'         => t('Style'),
      '#description'   => 'style',
      '#options'       => $likebtn_settings_style_options,
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_style']) ?
        $default_values['likebtn_settings_style'] : 'white') : $config->get('settings.likebtn_settings.style')),
    );
    $form['likebtn_settings_style_language']['likebtn_settings_lang'] = array(
      '#type'          => 'select',
      '#title'         => t('Language'),
      '#description'   => 'lang',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_lang']) ?
        $default_values['likebtn_settings_lang'] : 'en') : $config->get('settings.likebtn_settings.lang')),
      '#options'       => $likebtn_settings_lang_options,
    );

    $form['likebtn_settings_appearance_behaviour'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Appearance and behaviour'),
      '#weight'      => 5,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_like_label'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show "like"-label'),
      '#description'   => 'show_like_label',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_show_like_label']) ?
        $default_values['likebtn_settings_show_like_label'] : TRUE) : $config->get('settings.likebtn_settings.show_like_label')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_dislike_label'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show "dislike"-label'),
      '#description'   => 'show_dislike_label',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_show_dislike_label']) ?
        $default_values['likebtn_settings_show_dislike_label'] : FALSE) : $config->get('settings.likebtn_settings.show_dislike_label')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_dislike'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show popup on disliking'),
      '#description'   => 'popup_dislike',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_dislike']) ?
        $default_values['likebtn_settings_popup_dislike'] : FALSE) : $config->get('settings.likebtn_settings.popup_dislike')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_like_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show Like Button'),
      '#description'   => 'like_enabled',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_like_enabled']) ?
        $default_values['likebtn_settings_like_enabled'] : TRUE) : $config->get('settings.likebtn_settings.like_enabled')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_icon_like_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show like icon'),
      '#description'   => 'icon_like_show',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_icon_like_show']) ?
        $default_values['likebtn_settings_icon_like_show'] : TRUE) : $config->get('settings.likebtn_settings.icon_like_show')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_icon_dislike_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show dislike icon'),
      '#description'   => 'icon_dislike_show',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_icon_dislike_show']) ?
        $default_values['likebtn_settings_icon_dislike_show'] : TRUE) : $config->get('settings.likebtn_settings.icon_dislike_show')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_lazy_load'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Lazy load - if button is outside viewport it is loaded when user scrolls to it'),
      '#description'   => 'lazy_load',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_lazy_load']) ?
        $default_values['likebtn_settings_lazy_load'] : FALSE) : $config->get('settings.likebtn_settings.lazy_load')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_dislike_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show Dislike Button'),
      '#description'   => 'dislike_enabled',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_dislike_enabled']) ?
        $default_values['likebtn_settings_dislike_enabled'] : TRUE) : $config->get('settings.likebtn_settings.dislike_enabled')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_display_only'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Voting is disabled, display results only'),
      '#description'   => 'display_only',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_display_only']) ?
        $default_values['likebtn_settings_display_only'] : FALSE) : $config->get('settings.likebtn_settings.display_only')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_unlike_allowed'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Allow to unlike and undislike'),
      '#description'   => 'unlike_allowed',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_unlike_allowed']) ?
        $default_values['likebtn_settings_unlike_allowed'] : TRUE) : $config->get('settings.likebtn_settings.unlike_allowed')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_like_dislike_at_the_same_time'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Allow to like and dislike at the same time'),
      '#description'   => 'like_dislike_at_the_same_time',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_like_dislike_at_the_same_time']) ?
        $default_values['likebtn_settings_like_dislike_at_the_same_time'] : FALSE) : $config->get('settings.likebtn_settings.like_dislike_at_the_same_time')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_revote_period'] = array(
      '#type'          => 'textfield',
      '#title'         => t('The period of time in seconds after which it is allowed to vote again'),
      '#description'   => 'revote_period',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_revote_period']) ?
        $default_values['likebtn_settings_revote_period'] : '') : $config->get('settings.likebtn_settings.revote_period') ?: ''),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_show_copyright'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show copyright link in the share popup') . ' (VIP, ULTRA)',
      '#description'   => 'show_copyright',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_show_copyright']) ?
        $default_values['likebtn_settings_show_copyright'] : TRUE) : $config->get('settings.likebtn_settings.show_copyright')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_rich_snippet']) ?
        $default_values['likebtn_settings_rich_snippet'] : FALSE) : $config->get('settings.likebtn_settings.rich_snippet')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_html'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Custom HTML to insert into the popup') . ' (PRO, VIP, ULTRA)',
      '#description'   => 'popup_html',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_html']) ?
        $default_values['likebtn_settings_popup_html'] : '') : $config->get('settings.likebtn_settings.popup_html') ?: ''),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_donate']) ?
        $default_values['likebtn_settings_popup_donate'] : '') : $config->get('settings.likebtn_settings.popup_donate') ?: ''),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_content_order']) ?
        $default_values['likebtn_settings_popup_content_order'] : 'popup_share,popup_donate,popup_html') : $config->get('settings.likebtn_settings.popup_content_order')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show popop after "liking" (VIP, ULTRA)'),
      '#description'   => 'popup_enabled',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_enabled']) ?
        $default_values['likebtn_settings_popup_enabled'] : TRUE) : $config->get('settings.likebtn_settings.popup_enabled')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_position']) ?
        $default_values['likebtn_settings_popup_position'] : TRUE) : $config->get('settings.likebtn_settings.popup_position')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_style']) ?
        $default_values['likebtn_settings_popup_style'] : TRUE) : $config->get('settings.likebtn_settings.popup_style')),
      '#options'       => array(
        "light"  => "light",
        "dark" => "dark"),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_popup_hide_on_outside_click'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Hide popup when clicking outside'),
      '#description'   => 'popup_hide_on_outside_click',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_popup_hide_on_outside_click']) ?
        $default_values['likebtn_settings_popup_hide_on_outside_click'] : TRUE) : $config->get('settings.likebtn_settings.popup_hide_on_outside_click')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_event_handler'] = array(
      '#type'          => 'textfield',
      '#title'         => t('JavaScript callback function serving as an event handler'),
      '#description'   => 'event_handler<br/><br/>' . t('The provided function receives the event object as its single argument. The event object has the following properties: <strong>type</strong> – indicates which event was dispatched ("likebtn.loaded", "likebtn.like", "likebtn.unlike", "likebtn.dislike", "likebtn.undislike"); <strong>settings</strong> – button settings; <strong>wrapper</strong> – button DOM-element'),
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_event_handler']) ?
        $default_values['likebtn_settings_event_handler'] : NULL) : $config->get('settings.likebtn_settings.event_handler')),
    );
    $form['likebtn_settings_appearance_behaviour']['likebtn_settings_info_message'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show information message when the button can not be displayed due to misconfiguration'),
      '#description'   => 'info_message',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_info_message']) ?
        $default_values['likebtn_settings_info_message'] : TRUE) : $config->get('settings.likebtn_settings.info_message')),
    );

    $form['likebtn_settings_counter'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Counter'),
      '#weight'      => 6,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_type'] = array(
      '#type'          => 'select',
      '#title'         => t('Counter type'),
      '#description'   => 'counter_type',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_counter_type']) ?
        $default_values['likebtn_settings_counter_type'] : "number") : $config->get('settings.likebtn_settings.counter_type')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_counter_clickable']) ?
        $default_values['likebtn_settings_counter_clickable'] : FALSE) : $config->get('settings.likebtn_settings.counter_clickable')),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show votes counter'),
      '#description'   => 'counter_show',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_counter_show']) ?
        $default_values['likebtn_settings_counter_show'] : TRUE) : $config->get('settings.likebtn_settings.counter_show')),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_padding'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Counter padding'),
      '#description'   => 'counter_padding',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_counter_padding']) ?
        $default_values['likebtn_settings_counter_padding'] : NULL) : $config->get('settings.likebtn_settings.counter_padding')),
    );
    $form['likebtn_settings_counter']['likebtn_settings_counter_zero_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show zero value in counter'),
      '#description'   => 'counter_zero_show',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_counter_zero_show']) ?
        $default_values['likebtn_settings_counter_zero_show'] : FALSE) : $config->get('settings.likebtn_settings.counter_zero_show')),
    );

    $form['likebtn_settings_sharing'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Sharing'),
      '#weight'      => 7,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_sharing']['likebtn_settings_share_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show share buttons in the popup.') .  ' ' . t('Use popup_enabled option to enable/disable popup.') . ' (PLUS, PRO, VIP, ULTRA)',
      '#description'   => 'share_enabled',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_share_enabled']) ?
        $default_values['likebtn_settings_share_enabled'] : TRUE) : $config->get('settings.likebtn_settings.share_enabled')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_addthis_pubid']) ?
        $default_values['likebtn_settings_addthis_pubid'] : NULL) : $config->get('settings.likebtn_settings.addthis_pubid')),
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
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_addthis_service_codes']) ?
        $default_values['likebtn_settings_addthis_service_codes'] : NULL) : $config->get('settings.likebtn_settings.addthis_service_codes')),
      '#states' => array(
        // Disable field.
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
        ),
      ),
    );

    $form['likebtn_settings_loader'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Loader'),
      '#weight'      => 8,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_loader']['likebtn_settings_loader_show'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show loader while button is loading'),
      '#description'   => 'loader_show',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_loader_show']) ?
        $default_values['likebtn_settings_loader_show'] : FALSE) : $config->get('settings.likebtn_settings.loader_show')),
    );
    $form['likebtn_settings_loader']['likebtn_settings_loader_image'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Loader image URL (if empty, default image is used)'),
      '#description'   => 'loader_image',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_loader_image']) ?
        $default_values['likebtn_settings_loader_image'] : NULL) : $config->get('settings.likebtn_settings.loader_image')),
    );

    $form['likebtn_settings_tooltips'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Tooltips'),
      '#weight'      => 9,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );
    $form['likebtn_settings_tooltips']['likebtn_settings_tooltip_enabled'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Show tooltips'),
      '#description'   => 'tooltip_enabled',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_tooltip_enabled']) ?
        $default_values['likebtn_settings_tooltip_enabled'] : TRUE) : $config->get('settings.likebtn_settings.tooltip_enabled')),
    );

    $form['likebtn_settings_i18n'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Labels'),
      '#weight'      => 11,
      '#collapsible' => TRUE,
      '#collapsed'   => TRUE,
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_like'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button label'),
      '#description'   => 'i18n_like',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_like']) ?
        $default_values['likebtn_settings_i18n_like'] : NULL) : $config->get('settings.likebtn_settings.i18n_like')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_dislike'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button label'),
      '#description'   => 'i18n_dislike',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_dislike']) ?
        $default_values['likebtn_settings_i18n_dislike'] : NULL) : $config->get('settings.likebtn_settings.i18n_dislike')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_after_like'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button label after liking'),
      '#description'   => 'i18n_after_like',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_after_like']) ?
        $default_values['likebtn_settings_i18n_after_like'] : NULL) : $config->get('settings.likebtn_settings.i18n_after_like')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_after_dislike'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button label after disliking'),
      '#description'   => 'i18n_after_dislike',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_after_dislike']) ?
        $default_values['likebtn_settings_i18n_after_dislike'] : NULL) : $config->get('settings.likebtn_settings.i18n_after_dislike')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_like_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button tooltip'),
      '#description'   => 'i18n_like_tooltip',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_like_tooltip']) ?
        $default_values['likebtn_settings_i18n_like_tooltip'] : NULL) : $config->get('settings.likebtn_settings.i18n_like_tooltip')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_dislike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button tooltip'),
      '#description'   => 'i18n_dislike_tooltip',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_dislike_tooltip']) ?
        $default_values['likebtn_settings_i18n_dislike_tooltip'] : NULL) : $config->get('settings.likebtn_settings.i18n_dislike_tooltip')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_unlike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Like Button tooltip after "liking"'),
      '#description'   => 'i18n_unlike_tooltip',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_unlike_tooltip']) ?
        $default_values['likebtn_settings_i18n_unlike_tooltip'] : NULL) : $config->get('settings.likebtn_settings.i18n_unlike_tooltip')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_undislike_tooltip'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Dislike Button tooltip after "liking"'),
      '#description'   => 'i18n_undislike_tooltip',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_undislike_tooltip']) ?
        $default_values['likebtn_settings_i18n_undislike_tooltip'] : NULL) : $config->get('settings.likebtn_settings.i18n_undislike_tooltip')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_share_text'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Text displayed in share popup after "liking"'),
      '#description'   => 'i18n_share_text',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_share_text']) ?
        $default_values['likebtn_settings_i18n_share_text'] : NULL) : $config->get('settings.likebtn_settings.i18n_share_text')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_close'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Popup close button'),
      '#description'   => 'i18n_popup_close',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_popup_close']) ?
        $default_values['likebtn_settings_i18n_popup_close'] : NULL) : $config->get('settings.likebtn_settings.i18n_popup_close')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_text'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Popup text when sharing is disabled'),
      '#description'   => 'i18n_popup_text',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_popup_text']) ?
        $default_values['likebtn_settings_i18n_popup_text'] : NULL) : $config->get('settings.likebtn_settings.i18n_popup_text')),
    );

    $form['likebtn_settings_i18n']['likebtn_settings_i18n_popup_donate'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Text before donate buttons in the popup'),
      '#description'   => 'i18n_popup_donate',
      '#default_value' => ($default_values ? (isset($default_values['likebtn_settings_i18n_popup_donate']) ?
        $default_values['likebtn_settings_i18n_popup_donate'] : NULL) : $config->get('settings.likebtn_settings.i18n_popup_donate')),
    );

    $form['likebtn_settings_i18n']['likebtn_translate'] = array(
      '#type'          => 'item',
      '#description'   => t('<a href="https://likebtn.com/en/translate-like-button-widget" target="_blank">Send us translation</a>'),
    );

    $form['likebtn_demo_fieldset'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Demo'),
      '#weight'      => 12,
      '#collapsible' => FALSE,
      '#collapsed'   => FALSE,
    );

    /*
    $form['likebtn_demo_fieldset']['likebtn_demo'] = array(
      '#type'     => 'markup',
      '#markup'   => _likebtn_get_markup('live_demo', 1, $default_values),
    );
    */

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
}
