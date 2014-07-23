<?php
/**
*
* editor_of_attachments [Ukrainian]
*
* @package language editor_of_attachments
* @copyright (c) 2013 Oleksii Frychyn (Sherlock)
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
	'ACP_EDITOR_OF_ATTACHMENTS'				=> 'Редактор вкладень',
	'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN'		=> 'Розширені налаштування вкладень',
	
	'ACP_ATTACH_RESIZE'					=> 'Зменшення розміру вкладень [призначена група розширень: Зображення]',
	'ACP_ALLOW_ATTACH_RESIZE'			=> 'Включити зменшення розміру малюнків',
	'ACP_ALLOW_ATTACH_RESIZE_EXPLAIN'	=> 'Малюнки будуть автоматично зменшені при завантаженні до заданих розмірів. На вже завантажені малюнки включення даної опції не вплине.',
	'ACP_ATTACH_RESIZE_WIDHT'			=> 'Ширина малюнків',
	'ACP_ATTACH_RESIZE_HEIGHT'			=> 'Висота малюнків',
	
	'ACP_QUOTE_ATTACH'					=> 'Цитування вкладень',
	'ACP_ALLOW_QUOTE_ATTACH'			=> 'Включити цитування вкладень',
	'ACP_ALLOW_QUOTE_ATTACH_EXPLAIN'	=> 'При цитуванні повідомлення вкладення в цитаті будуть замінені посиланнями (зображення в bbcode [img], інші типи вкладень в bbcode [url]).',
));
