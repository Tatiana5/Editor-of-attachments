<?php
/**
*
* editor_of_attachments [English]
*
* @package language editor_of_attachments
* @copyright (c) 2014 Татьяна5
* @implementer 2015 Anvar [bb3.mobi]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_EDITOR_OF_ATTACHMENTS'			=> 'Editor attachments',
	'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN'	=> 'Advanced Settings attachments',
	'ACL_A_EDITOR_ATTACH'	=> 'Can change the advanced settings attachments',

	'ACP_ATTACH_IMG_QUALITY'			=> 'Quality JPG',
	'ACP_ATTACH_IMG_QUALITY_EXPLAIN'	=> '',

	'ACP_QUOTE_ATTACH'					=> 'Quote attachments',
	'ACP_ALLOW_QUOTE_ATTACH'			=> 'Enable quoting attachments',
	'ACP_ALLOW_QUOTE_ATTACH_EXPLAIN'	=> 'When quoting Posts attachments in the quotation will be replaced by references (image bbcode [img], other types of attachments in the bbcode [url]).',

	'ACP_WATERMARK_ATTACH'			=> 'Watermark',
	'CREATE_WATERMARK'				=> 'Tnable &laquo;watermark&raquo;',
	'PERCENT'						=> 'precent',
	'WATERMARK_OPACITY'				=> 'Transparency',
	'WATERMARK_OPACITY_EXPLAIN'		=> 'Transparency is a percentage: <br /> 100% - fully opaque, 0% - fully transparent.',
	'WATERMARK_POSITION'			=> 'Position',
	'WATERMARK_POSITION_EXPLAIN'	=> 'Select the location of the watermark on the image.',
	'USER_CONFIRM_WATERMARK'		=> 'Allow user choice',
	'USER_CONFIRM_WATERMARK_EXPLAIN' => 'Allow user enable/disable create watermark when downloading attachment.',
	'WM_IMG_TYPE'					=> 'Permitted types',
	'WM_IMG_TYPE_EXPLAIN'			=> 'Types of images, for which enabled create watermark (jpg, gif or png). For once, you can select all, two or one type by selecting them with the appropriate combination of mouse and keyboard for your computer and browser. The permitted types are highlighted in a special color.',
	'WM_IMG_MIN_SIZE'				=> 'The minimum size of the images',
	'WM_IMG_MIN_SIZE_EXPLAIN'		=> 'If the image will be less than the specified width and height, the watermark will not be attached.',

	'position'	=> array(
		'right_bottom'			=> 'right bottom',
		'right_top'				=> 'right top',
		'left_top'				=> 'left top',
		'left_bottom'			=> 'left bottom',
		'center'				=> 'center',
		'rightt_center'			=> 'rightt_center',
		'left_center'			=> 'left_center',
	),
));