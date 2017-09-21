<?php
/**
 *
 * Editor of attachments. An extension for the phpBB Forum Software package.
 * French translation by samemmerde (http://www.ezcom-fr.com/memberlist.php?mode=viewprofile&u=296) & Galixte (http://www.galixte.com)
 *
 * @copyright (c) 2014 Татьяна5, 2015 Anvar <bb3.mobi>
 * @license GNU General Public License, version 2 (GPL-2.0)
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

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ « » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_EDITOR_OF_ATTACHMENTS'			=> 'Présentation des fichiers joints cités',
	'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN'	=> 'Paramètres',
	'ACL_A_EDITOR_ATTACH'				=> 'Permet de modifier les paramètres avancés des fichiers joints',

	'ACP_ATTACH_IMG_QUALITY'			=> 'Qualité JPG',
	'ACP_ATTACH_IMG_QUALITY_EXPLAIN'	=> '',

	'ACP_QUOTE_ATTACH'					=> 'Citation des fichiers joints',
	'ACP_ALLOW_QUOTE_ATTACH'			=> 'Activer les citations des fichiers joints',
	'ACP_ALLOW_QUOTE_ATTACH_EXPLAIN'	=> 'Permet de remplacer les références aux fichiers joints cités dans les messages, telles que : image entre les balises du BBCode [img], ou tout autre types de fichiers joints entre les balises du BBCode [url].',

	'ACP_WATERMARK_ATTACH'			=> 'Filigrane',
	'CREATE_WATERMARK'				=> 'Activer l’affichage du &laquo; filigrane &raquo;',
	'PERCENT'						=> 'Pourcentage',
	'WATERMARK_OPACITY'				=> 'Transparence',
	'WATERMARK_OPACITY_EXPLAIN'		=> 'Permet de saisir le pourcentage de transparence : <br /> 100% - complètement opaque, 0% - totalement transparent.',
	'WATERMARK_POSITION'			=> 'Position',
	'WATERMARK_POSITION_EXPLAIN'	=> 'Permet de sélectionner la position du filigrane sur l’image.',
	'USER_CONFIRM_WATERMARK'		=> 'Autoriser le choix aux utilisateurs',
	'USER_CONFIRM_WATERMARK_EXPLAIN'=> 'Permettre à l’utilisateur d’activer / désactiver le filigrane lors de la publication de fichier joint.',
	'WM_IMG_TYPE'					=> 'Types d’images autorisés',
	'WM_IMG_TYPE_EXPLAIN'			=> 'Permet de sélectionner les types d’images pour lesquels il sera possible de créer un filigrane (jpg, gif ou png). Pour sélectionner un ou plusieurs format, il est nécessaire d’utiliser la combinaison de la touche CTRL et du clic gauche de la souris. Les types autorisés sont mis en évidence dans une couleur différente.',
	'WM_IMG_MIN_SIZE'				=> 'Dimensions minimales des images',
	'WM_IMG_MIN_SIZE_EXPLAIN'		=> 'Permet de saisir les dimensions minimales en dessous desquelles le filigrane ne sera pas affiché.',

	'position'	=> array(
		'right_bottom'			=> 'En bas à droite',
		'right_top'				=> 'En haut à droite',
		'left_top'				=> 'En haut à gauche',
		'left_bottom'			=> 'En bas à gauche',
		'center'				=> 'Au centre',
		'rightt_center'			=> 'Centré à droite',
		'left_center'			=> 'Centré à gauche',
	),
));