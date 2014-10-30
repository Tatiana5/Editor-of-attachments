<?php
/**
*
* quickreply [Russian]
*
* @package language quickreply
* @copyright (c) 2013 Татьяна5
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
	'ACP_EDITOR_OF_ATTACHMENTS'				=> 'Редактор вложений',
	'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN'		=> 'Расширенные настройки вложений',
	
	'ACP_ATTACH_RESIZE'					=> 'Уменьшение размера вложений [назначенная группа расширений: Изображения]',
	'ACP_ALLOW_ATTACH_RESIZE'			=> 'Включить уменьшение размера рисунков',
	'ACP_ALLOW_ATTACH_RESIZE_EXPLAIN'	=> 'Рисунки будут автоматически уменьшены при загрузке до заданных размеров. На уже загруженные рисунки включение данной опции не повлияет.',
	'ACP_ATTACH_RESIZE_WIDHT'			=> 'Ширина рисунков',
	'ACP_ATTACH_RESIZE_HEIGHT'			=> 'Высота рисунков',
	
	'ACP_QUOTE_ATTACH'					=> 'Цитирование вложений',
	'ACP_ALLOW_QUOTE_ATTACH'			=> 'Включить цитирование вложений',
	'ACP_ALLOW_QUOTE_ATTACH_EXPLAIN'	=> 'При цитировании сообщения вложения в цитате будут заменены ссылками (изображения в bbcode [img], остальные типы вложений в bbcode [url]).',
));