<?php
/**
 * Created by PhpStorm.
 * User: znak
 * Date: 29.01.17
 * Time: 15:29
 */

namespace Drupal\likebtn\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\likebtn\LikeBtn;

class LikeBtnController extends ControllerBase {
  public function likes($entity, $entity_type) {
    $output = '';

    $rows = likebtn_get_count($entity, $entity_type);

    $total_likes_minus_dislikes = 0;
    foreach ($rows as $row) {
      $total_likes_minus_dislikes += $row['likes_minus_dislikes'];
    }

    $header = array(
      t('Button'),
      t('Likes'),
      t('Dislikes'),
      t('Likes minus dislikes'),
    );
    $output .= theme('table', array('header' => $header, 'rows' => $rows));

    $output .= '<p>' . t('Total likes minus dislikes (vote results):') . ' <strong> ' . $total_likes_minus_dislikes . '</strong></p>';
    $output .= '<p>' . t("If you don't see information on likes:") . '</p>';
    $output .= '<ul>';
    $output .= '<li>' . t('Make sure you have entered information correctly in') . ' <a href="/admin/config/services/likebtn">' . t('Auto-synching likes into local database') . '(PRO, VIP, ULTRA)</a></li>';
    $output .= '<li>' . t('Make sure that PHP curl extension is enabled.') . '</a></li>';
    $output .= '<li>' . t('Maybe nobody voted for this content type yet.') . '</a></li>';
    $output .= '<li>' . t('Perhaps synchronization has not been launched yet.') . '</a></li>';
    $output .= '</ul>';

    return $output;
  }

  public function likebtnTestSync () {
    $likebtn_account_email = '';
    if (isset($_POST['likebtn_account_email'])) {
      $likebtn_account_email = $_POST['likebtn_account_email'];
    }

    $likebtn_account_api_key = '';
    if (isset($_POST['likebtn_account_api_key'])) {
      $likebtn_account_api_key = $_POST['likebtn_account_api_key'];
    }

    $likebtn_account_site_id = '';
    if (isset($_POST['likebtn_account_site_id'])) {
      $likebtn_account_site_id = $_POST['likebtn_account_site_id'];
    }

    // Run test.
    $likebtn = new LikeBtn();

    $test_response = $likebtn->testSync($likebtn_account_email, $likebtn_account_api_key, $likebtn_account_site_id);

    if ($test_response['result'] == 'success') {
      $result_text = t('OK');
    }
    else {
      $result_text = t('Error');
    }

    $response = array(
      'result' => $test_response['result'],
      'result_text' => $result_text,
      'message' => $test_response['message'],
    );

    ob_clean();
    echo json_encode($response);
  }
}
