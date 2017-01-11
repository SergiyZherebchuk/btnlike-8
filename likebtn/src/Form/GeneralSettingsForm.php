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

class GeneralSettingsForm extends ConfigFormBase {

  protected function getEditableConfigNames() {
    return [
      'likebtn.settings'
    ];
  }

  public function getFormId() {
    return 'likebtn_general_settings';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $public_url = _likebtn_public_url();
    $config = $this->config('likebtn.settings');

    $form['#attached']['js'][] = array(
      'type' => 'file',
      'data' => drupal_get_path('module', 'likebtn') . "/assets/js/admin.js"
    );

    $form = array();

    // Get all available content types.
    $types = NodeType::loadMultiple();
    $options = array();
    foreach ($types as $type) {
      $options[$type->id()] = $type->get('name');
    }

    // Get all available entities view modes.
    $view_modes = \Drupal::entityTypeManager()->getDefinition('node');
    foreach ($view_modes as $view_mode_id => $view_mode_info) {
      $view_modes_options[$view_mode_id] = $view_mode_info['label'];
    }

    $form['likebtn_plan'] = array(
      '#type'          => 'select',
      '#title'         => t('Website tariff plan'),
      '#description'   => t('Specify your website <a href="http://likebtn.com/en/#plans_pricing">plan</a>. The plan specified determines available settings.'),
      '#default_value' => $config->get('general.likebtn_plan'),
      '#options'       => array(
        LIKEBTN_PLAN_TRIAL => 'TRIAL',
        LIKEBTN_PLAN_FREE => 'FREE',
        LIKEBTN_PLAN_PLUS => 'PLUS',
        LIKEBTN_PLAN_PRO => 'PRO',
        LIKEBTN_PLAN_VIP => 'VIP',
        LIKEBTN_PLAN_ULTRA => 'ULTRA',
      ),
    );

    $form['likebtn_general_display_options'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('General display options'),
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    );

    $form['likebtn_general_display_options']['likebtn_hint'] = array(
      '#type'          => 'item',
      '#description'   => t('You can set up the Like Button globally on this page, or per content type as a field in <a href="@link-manage_fields">Structure » Content types » Manage fields</a>.') . '<br/>' . t('Keep in mind that only websites upgraded to <a href="http://likebtn.com/en/#plans_pricing" target="_blank">PLUS</a> plan or higher are allowed to display more then 10 like buttons per page.',
          array(
            '@link-manage_fields' => Url::fromRoute('admin/structure/types'),
          )
        ),
    );

    $form['likebtn_general_display_options']['likebtn_nodetypes'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Enable for the following content types'),
      '#description'   => t('Select the content types for which you want to activate like button.'),
      '#default_value' => $config->get('general.likebtn_nodetypes') ?: array(
        'article' => 'article',
        'page' => 'page'
      ),
      '#options'       => $options,
    );

    $form['likebtn_general_display_options']['likebtn_comments_nodetypes'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Enable for comments to the following content types'),
      '#description'   => t('Select the content types for comments to which you want to activate like button.'),
      '#default_value' => $config->get('general.likebtn_comments_nodetypes') ?: array(),
      '#options'       => $options,
      '#disabled'      => !\Drupal::moduleHandler()->moduleExists('comment'),
    );

    $form['likebtn_general_display_options']['likebtn_view_modes'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Entities view modes'),
      '#description'   => t('When will the like button be displayed?'),
      '#default_value' => $config->get('general.likebtn_view_modes') ?: array(
        'full' => 'full',
        'teaser' => 'teaser',
      ),
      '#options'       => $view_modes_options,
    );

    $form['likebtn_general_display_options']['likebtn_weight'] = array(
      '#type'          => 'select',
      '#title'         => t('Position'),
      '#description'   => t('The more the weight, the lower like button position in the entity.'),
      '#default_value' => $config->get('general.likebtn_weight'),
      '#options'       => array(
        -100 => '-100',
        -50  => '-50',
        -20  => '-20',
        -10  => '-10',
        -5   => '-5',
        5    => '5',
        10   => '10',
        20   => '20',
        50   => '50',
        100  => '100',
      ),
    );

    $form['likebtn_general_display_options']['likebtn_user_logged_in'] = array(
      '#type'          => 'select',
      '#title'         => t('User authorization'),
      '#description'   => t('Show the Like Button when user is logged in, not logged in or show for all.'),
      '#default_value' => $config->get('general.likebtn_user_logged_in'),
      '#options'       => array(
        'all' => t('For all'),
        'logged_in' => t('Logged in'),
        'not_logged_in' => t('Not logged in'),
      ),
    );

    $form['likebtn_account_data'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Account Details'),
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    );
    $form['likebtn_account_data']['likebtn_hint_account_data'] = array(
      '#type'          => 'item',
      '#description'   => t('Fill in these fields if you want information on likes to be periodically fetched from LikeBtn.com system into your database. It would allow to sort content in views by vote results using Voting API or LikeBtn field.'),
    );
    $form['likebtn_account_data']['likebtn_account_data_email'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Email'),
      '#default_value' => $config->get('general.likebtn_account_data_email'),
      '#description'   => t('Your LikeBtn.com account email (can be found on <a href="http://likebtn.com/en/customer.php/profile/edit" target="_blank">Profile page</a>)'),
    );
    $form['likebtn_account_data']['likebtn_account_data_api_key'] = array(
      '#type'          => 'textfield',
      '#title'         => t('API key'),
      '#maxlength'     => 32,
      '#default_value' => $config->get('general.likebtn_account_data_api_key'),
      '#description'   => t('Your website API key on LikeBtn.com (can be requested on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites page</a>)'),
    );
    $form['likebtn_account_data']['likebtn_account_data_site_id'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Site ID'),
      '#maxlength'     => 24,
      '#default_value' => $config->get('general.likebtn_account_data_site_id'),
      '#description'   => t('Your Site ID on LikeBtn.com. Can be obtained on <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites page</a>. If your website has multiple addresses or you are developing a website on a local server and planning to move it to a live domain, you can add domains to the website <a href="http://likebtn.com/en/customer.php/websites">here</a>.'),
    );
    $form['likebtn_sync'] = array(
      '#type'        => 'fieldset',
      '#title'       => t('Synchronization') . ' (PRO, VIP, ULTRA)',
      '#collapsible' => TRUE,
      '#collapsed'   => FALSE,
    );

    $form['likebtn_sync']['likebtn_hint_sync'] = array(
      '#type'          => 'item',
      '#description'   => t('Requirements:') . '<ul><li>' . t('Your website must be upgraded to <a href="http://likebtn.com/en/#plans_pricing" target="_blank">PRO</a> or higher on <a href="http://likebtn.com/en/#plans_pricing" target="_blank">LikeBtn.com</a>.') . '</li><li>' . t('PHP curl extension must be enabled.') . '</li></ul>',
    );

    $form['likebtn_sync']['likebtn_sync_inerval'] = array(
      '#type'          => 'select',
      '#title'         => t('Synchronization interval'),
      '#description'   => t('Time interval in minutes in which fetching of likes from LikeBtn.com into your database is being launched. The less the interval the heavier your database load (60 minutes interval is recommended)'),
      '#default_value' => $config->get('general.likebtn_sync_inerval') ?: 60,
      '#options'       => array(
        5 => '5',
        15 => '15',
        30 => '30',
        60 => '60',
        90 => '90',
        120 => '120',
      ),
      '#states' => array(
        'disabled' => array(
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_FREE)),
          array(':input[name="likebtn_plan"]' => array('value' => LIKEBTN_PLAN_PLUS)),
        ),
      ),
    );
    $form['likebtn_sync']['likebtn_test_sync'] = array(
      '#type' => 'markup',
      '#markup' => '<button onclick="return testSync(\'' . _likebtn_subdirectory() . 'admin/config/services/likebtn/testsync\')" >' . t('Test synchronization') . '</button> &nbsp;<strong class="likebtn_test_sync_container"><img src="' . $public_url . '/assets/img/ajax_loader.gif" style="display:none"/><span class="likebtn_test_sync_message"></span></strong>',
    );

    $form['likebtn_settings_local_domain'] = array(
      '#type'          => 'hidden',
      '#title'         => t('Local domain'),
      '#description'   => t('Example:') . ' localdomain!50f358d30acf358d30ac000001. ' . t('Specify it if your website is located on a local server and is available from your local network only and NOT available from the Internet. You can find the domain on your <a href="http://likebtn.com/en/customer.php/websites" target="_blank">Websites</a> page after adding your local website to the panel. See <a href="http://likebtn.com/en/faq#local_domain" target="_blank">FAQ</a> for more details.'),
      '#default_value' => $config->get('general.likebtn_settings_local_domain'),
    );

    $form['likebtn_settings_subdirectory'] = array(
      '#type'          => 'hidden',
      '#title'         => t('Website subdirectory'),
      '#description'   => t('If your website is one of websites located in different subdirectories of one domain and you want to have a statistics separate from other websites on this domain, enter subdirectory (for example /subdirectory/).'),
      '#default_value' => $config->get('general.likebtn_settings_subdirectory'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement validateForm() method.
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $config = $this->config('likebtn.settings');

    $config->set('general.likebtn_plan', $values['likebtn_plan'])
      ->set('general.likebtn_nodetypes', $values['likebtn_nodetypes'])
      ->set('general.likebtn_comments_nodetypes', $values['likebtn_comments_nodetypes'])
      ->set('general.likebtn_view_modes', $values['likebtn_view_modes'])
      ->set('general.likebtn_user_logged_in', $values['likebtn_user_logged_in'])
      ->set('general.likebtn_account_data_email', $values['likebtn_account_data_email'])
      ->set('general.likebtn_account_data_api_key', $values['likebtn_account_data_api_key'])
      ->set('general.likebtn_account_data_site_id', $values['likebtn_account_data_site_id'])
      ->set('general.likebtn_sync_inerval', $values['likebtn_sync_inerval'])
      ->set('general.likebtn_settings_local_domain', $values['likebtn_settings_local_domain'])
      ->set('general.likebtn_settings_subdirectory', $values['likebtn_settings_subdirectory'])
      ->set('general.likebtn_weight', $values['likebtn_weight'])
      ->save();

    parent::submitForm($form, $form_state);
  }
}
