<?php

namespace Drupal\likebtn;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

interface LikebtnInterface extends ConfigEntityInterface {
  /**
   * Module name
   */
  const LIKEBTN_MODULE_NAME = 'likebtn';
  const LIKEBTN_VERSION = '1.12';

  /**
   * Views widget display oprions
   */
  const LIKEBTN_VIEWS_WIDGET_DISPLAY_ONLY = 1;
  const LIKEBTN_VIEWS_WIDGET_FULL = 2;

  /**
   * LikeBtn plans
   */
  const LIKEBTN_PLAN_TRIAL = 9;
  const LIKEBTN_PLAN_FREE = 0;
  const LIKEBTN_PLAN_PLUS = 1;
  const LIKEBTN_PLAN_PRO = 2;
  const LIKEBTN_PLAN_VIP = 3;
  const LIKEBTN_PLAN_ULTRA = 4;

  /**
   * Comments sort by
   */
  const LIKEBTN_COMMENTS_SORT_BY_LIKES = 'likes';
  const LIKEBTN_COMMENTS_SORT_BY_DISLIKES = 'dislike';
  const LIKEBTN_COMMENTS_SORT_BY_LIKES_MINUS_DISLIKES = 'likes_minus_dislikes';

  /**
   * Comments sort order
   */
  const LIKEBTN_COMMENTS_SORT_ORDER_ASC = 'asc';
  const LIKEBTN_COMMENTS_SORT_ORDER_DESC = 'desc';

  /**
   * LikeBtn shortcode
   */
  const LIKEBTN_SHORTCODE = 'likebtn';

  /**
   * Voting API tag name
   */
  const LIKEBTN_VOTING_TAG = 'likebtn';

  /**
   * Voting API vote source of the vote cast on the entity
   */
  const LIKEBTN_VOTING_VOTE_SOURCE = 'entity';

  /**
   * Another
   */
  const LIKEBTN_LAST_SUCCESSFULL_SYNC_TIME_OFFSET = 57600;
  const LIKEBTN_LOCALES_SYNC_INTERVAL = 57600;
  const LIKEBTN_STYLES_SYNC_INTERVAL = 57600;
  const LIKEBTN_API_URL = 'http://api.likebtn.com/api/';

	/**
	 * Arrays
	 */
	const LIKEBTN_SETTINGS = array(
		"identifier"             => array("default" => ''),
		"local_domain"           => array("default" => ''),
		"domain_from_parent"     => array("default" => FALSE),
		"subdirectory"           => array("default" => ''),
		"lang"                   => array("default" => "en"),
		"show_like_label"        => array("default" => TRUE),
		"show_dislike_label"     => array("default" => FALSE),
		"popup_dislike"          => array("default" => FALSE),
		"like_enabled"           => array("default" => TRUE),
		"dislike_enabled"        => array("default" => TRUE),
		"icon_like_show"         => array("default" => TRUE),
		"icon_dislike_show"      => array("default" => TRUE),
		"lazy_load"              => array("default" => FALSE),
		"counter_type"           => array("default" => "number"),
		"counter_clickable"      => array("default" => FALSE),
		"counter_show"           => array("default" => TRUE),
		"counter_padding"        => array("default" => ''),
		"counter_zero_show"      => array("default" => FALSE),
		"display_only"           => array("default" => FALSE),
		"unlike_allowed"         => array("default" => TRUE),
		"like_dislike_at_the_same_time" => array("default" => FALSE),
		"revote_period"          => array("default" => ''),
		"style"                  => array("default" => 'white'),
		"share_enabled"          => array("default" => TRUE),
		"item_url"               => array("default" => ''),
		"item_title"             => array("default" => ''),
		"item_description"       => array("default" => ''),
		"item_image"             => array("default" => ''),
		"item_date"              => array("default" => ''),
		"addthis_pubid"          => array("default" => ''),
		"addthis_service_codes"  => array("default" => ''),
		"loader_show"            => array("default" => FALSE),
		"loader_image"           => array("default" => ''),
		"tooltip_enabled"        => array("default" => TRUE),
		"show_copyright"         => array("default" => TRUE),
		"rich_snippet"           => array("default" => FALSE),
		"popup_html"             => array("default" => ''),
		"popup_donate"           => array("default" => ''),
		"popup_content_order"    => array("default" => 'popup_share,popup_donate,popup_html'),
		"popup_enabled"          => array("default" => TRUE),
		"popup_position"         => array("default" => 'top'),
		"popup_style"            => array("default" => 'light'),
		"popup_hide_on_outside_click"  => array("default" => TRUE),
		"event_handler"          => array("default" => ''),
		"info_message"           => array("default" => ''),
		"i18n_like"              => array("default" => ''),
		"i18n_dislike"           => array("default" => ''),
		"i18n_after_like"        => array("default" => ''),
		"i18n_after_dislike"     => array("default" => ''),
		"i18n_like_tooltip"      => array("default" => ''),
		"i18n_dislike_tooltip"   => array("default" => ''),
		"i18n_unlike_tooltip"    => array("default" => ''),
		"i18n_undislike_tooltip" => array("default" => ''),
		"i18n_share_text"        => array("default" => ''),
		"i18n_popup_close"       => array("default" => ''),
		"i18n_popup_text"        => array("default" => ''),
		"i18n_popup_donate"      => array("default" => ''));
	const LIKEBTN_LANGS = array(
		'en' => '[en] - English',
		'ru' => '[ru] - Русский',
		'af' => '[af] - Afrikaans',
		'ar' => '[ar] - العربية',
		'hy' => '[hy] - Հայերեն',
		'bn' => '[bn] - বাংলা',
		'bg' => '[bg] - Български език',
		'ca' => '[ca] - Català',
		'zh_CN' => '[zh_CN] - 简体中文',
		'cs' => '[cs] - Čeština',
		'nl' => '[nl] - Nederlands',
		'fa' => '[fa] - فارسی',
		'fi' => '[fi] - Suomi',
		'fr' => '[fr] - Français',
		'da' => '[da] - Dansk',
		'de' => '[de] - Deutsch',
		'el' => '[el] - Ελληνικά',
		'he' => '[he] - עברית',
		'hu' => '[hu] - Hungarian',
		'id' => '[id] - Bahasa Indonesia',
		'it' => '[it] - Italiano',
		'ja' => '[ja] - 日本語',
		'kk' => '[kk] - Қазақ тілі',
		'ko' => '[ko] - 한국어',
		'lt' => '[lt] - Lietuvių kalba',
		'ne' => '[ne] - नेपाली',
		'no' => '[no] - Norsk bokmål',
		'pl' => '[pl] - Polski',
		'pt' => '[pt] - Português',
		'pt_BR' => '[pt_BR] - Português do Brasil',
		'ro' => '[ro] - Română',
		'es' => '[es] - Español',
		'sv' => '[sv] - Svenska',
		'th' => '[th] - ไทย',
		'tr' => '[tr] - Türkçe',
		'uk' => '[uk] - Українська мова',
		'vi' => '[vi] - Tiếng Việt');
	const LIKEBTN_STYLES = array(
		'white',
		'lightgray',
		'gray',
		'black',
		'padded',
		'drop',
		'line',
		'github',
		'transparent',
		'youtube',
		'habr',
		'heartcross',
		'plusminus',
		'google',
		'greenred',
		'large',
		'elegant',
		'disk',
		'squarespace',
		'slideshare',
		'baidu',
		'uwhite',
		'ublack',
		'uorange',
		'ublue',
		'ugreen',
		'direct',
		'homeshop');
	// LikeBtn website locales available.
	const LIKEBTN_WEBSITE_LOCALES = array("en",	"ru");
}
