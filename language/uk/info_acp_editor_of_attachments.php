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
	'ACP_EDITOR_OF_ATTACHMENTS'			=> 'Редактор вкладень',
	'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN'	=> 'Розширені налаштування вкладень',
	'ACL_A_EDITOR_ATTACH'				=> 'Може змінювати розширені налаштування вкладень',

	'ACP_ATTACH_IMG_QUALITY'			=> 'Качество JPG',
	'ACP_ATTACH_IMG_QUALITY_EXPLAIN'	=> 'При уменьшении изображений или наложении watermark размер файла может вырасти. С помощью этой опции можно изменить степень сжатия, чтобы сэкономить дисковое пространство.',

	'ACP_QUOTE_ATTACH'					=> 'Цитування вкладень',
	'ACP_ALLOW_QUOTE_ATTACH'			=> 'Включити цитування вкладень',
	'ACP_ALLOW_QUOTE_ATTACH_EXPLAIN'	=> 'При цитуванні повідомлення вкладення в цитаті будуть замінені посиланнями (зображення в bbcode [img], інші типи вкладень в bbcode [url]).',

	'ACP_WATERMARK_ATTACH'			=> 'Водяной знак',
	'CREATE_WATERMARK'				=> 'Включить функцию &laquo;водяного знака&raquo;',
	'PERCENT'						=> 'процентов',
	'WATERMARK_OPACITY'				=> 'Прозрачность',
	'WATERMARK_OPACITY_EXPLAIN'		=> 'Прозрачность задается в процентах:<br />100% - полностью непрозрачный, 0% - полностью прозрачный.',
	'WATERMARK_POSITION'			=> 'Расположение',
	'WATERMARK_POSITION_EXPLAIN'	=> 'Выберите расположение водяного знака на изображении.',
	'USER_CONFIRM_WATERMARK'		=> 'Разрешить выбор пользователю',
	'USER_CONFIRM_WATERMARK_EXPLAIN'=> 'Разрешить пользователю включать/выключать создание водяного знака при загрузке вложения.',
	'WM_IMG_TYPE'					=> 'Разрешенные типы',
	'WM_IMG_TYPE_EXPLAIN'			=> 'Типы изображений, для которых разрешено создание водяного знака (jpg, gif или png). За один раз можно выбрать все, два или один тип, выбрав их с помощью соответствующей комбинации мыши и клавиатуры вашего компьютера и браузера. Разрешенные типы выделены особым цветом.',
	'WM_IMG_MIN_SIZE'				=> 'Минимальный размер изображений',
	'WM_IMG_MIN_SIZE_EXPLAIN'		=> 'Если изображения будут меньше указанной ширины и высоты, то водяной знак прикреплен не будет.',
));
