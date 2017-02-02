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
use Drupal\likebtn\LikebtnInterface;

class LikeBtnController extends ControllerBase {

	function likebtn_likes_page($entity, $entity_type) {
		$rows = $this->likebtn_get_count($entity, $entity_type);
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

		$result = array(
			'total_likes_minus_dislikes' => $total_likes_minus_dislikes,
			'header' => $header,
			'rows' => $rows
		);

		return $result;
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

	/**
	 * Get likes and dislikes count for the node.
	 */
	private function likebtn_get_count($entity, $entity_type) {
		list($entity_id, $entity_revision_id, $bundle) = entity_extract_ids($entity_type, $entity);

		try {
			$query = db_select('votingapi_vote', 'vv')
				->fields('vv')
				->condition('vv.entity_type', $entity_type)
				->condition('vv.entity_id', $entity_id)
				->condition('vv.value_type', 'points')
				->condition('vv.tag', LikebtnInterface::LIKEBTN_VOTING_TAG)
				->orderBy('vv.vote_source', 'ASC');

			$votingapi_results = $query->execute();
		}
		catch (Exception $e) {
			return $e;
		}

		// Display a table with like counts per button.
		$rows = array();
		// Like and dislike rows has been found.
		$records_by_source  = array();

		while (1) {
			$record = $votingapi_results->fetchAssoc();

			// Records with likes and dislikes go one after another.
			if (!count($records_by_source) || $record['vote_source'] == $records_by_source[count($records_by_source) - 1]['vote_source']) {
				// Do nothing.
			}
			elseif (count($records_by_source)) {
				$first_record  = $records_by_source[0];
				$second_record = array('value' => 0);
				if (!empty($records_by_source[1])) {
					$second_record = $records_by_source[1];
				}

				if ($first_record['value'] >= 0 && $second_record['value'] <= 0) {
					$likes    = $first_record['value'];
					$dislikes = abs($second_record['value']);
				}
				else {
					$likes    = $second_record['value'];
					$dislikes = abs($first_record['value']);
				}
				$likes_minus_dislikes = $likes - $dislikes;

				$rows[] = array(
					'button' => _likebtn_get_name($first_record['vote_source']),
					'likes' => $likes,
					'dislikes' => $dislikes,
					'likes_minus_dislikes' => $likes_minus_dislikes,
				);

				$records_by_source = array();
			}
			$records_by_source[] = $record;

			if (!$record) {
				break;
			}
		}

		return $rows;
	}
}
