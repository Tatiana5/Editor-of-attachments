<?php
/**
*
* @package editor_of_attachments
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\editor_of_attachments\acp;

class editor_of_attachments_module
{
	var $u_action;

	function main($id, $mode)
	{
		global $config, $db, $user, $auth, $template, $request;
		global $phpbb_root_path, $phpEx;

		$this->page_title = 'ACP_EDITOR_OF_ATTACHMENTS';
		$this->tpl_name = 'acp_board';

		$submit = $request->is_set_post('submit');

		$form_key = 'config_editor_of_attachments';
		add_form_key($form_key);

		$display_vars = array(
			'title'	=> 'ACP_EDITOR_OF_ATTACHMENTS',
			'vars'	=> array(
				'legend1'		=> 'ACP_QUOTE_ATTACH',
				'allow_quote_attach'	=> array('lang' => 'ACP_ALLOW_QUOTE_ATTACH', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),

				'legend2'				=> 'ACP_WATERMARK_ATTACH',
				'img_create_watermark'		=> array('lang' => 'CREATE_WATERMARK',		'validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => false),
				//'user_confirm_watermark'	=> array('lang' => 'USER_CONFIRM_WATERMARK','validate' => 'bool',	'type' => 'radio:yes_no', 'explain' => true),
				'watermark_opacity'			=> array('lang' => 'WATERMARK_OPACITY',		'validate' => 'int',	'type' => 'text:3:3', 'explain' => true, 'append' => ' ' . $user->lang['PERCENT']),
				'attach_img_quality'	=> array('lang' => 'ACP_ATTACH_IMG_QUALITY', 'validate' => 'int:0', 'type' => 'number:50:99', 'explain' => true, 'append' => ' ' . $user->lang['PERCENT']),
				'default_position'			=> array('lang' => 'WATERMARK_POSITION',	'validate' => 'string', 'type' => 'custom', 'method' => 'position_select', 'params' => array('{CONFIG_VALUE}', 1), 'explain' => true),
				'watermark_min_img_size'	=> array('lang' => 'WM_IMG_MIN_SIZE',	'validate' => 'int',	'type' => 'text:3:3', 'explain' => true, 'append' => ' ' . $user->lang['PIXEL']),
				'wm_img_type'				=> array('lang' => 'WM_IMG_TYPE',		'validate' => 'string', 'type' => 'custom', 'method' => 'img_select_type', 'explain' => true),

				'legend3'				=> 'ACP_SUBMIT_CHANGES',
			),
		);
						
		if (isset($display_vars['lang']))
		{
			$user->add_lang($display_vars['lang']);
		}

		$this->new_config = $config;
		$cfg_array = ($request->is_set('config')) ? utf8_normalize_nfc($request->variable('config', array('' => ''), true)) : $this->new_config;
		$error = array();

		// We validate the complete config if wished
		validate_config_vars($display_vars['vars'], $cfg_array, $error);

		if ($submit && !check_form_key($form_key))
		{
			$error[] = $user->lang['FORM_INVALID'];
		}
		// Do not write values if there is an error
		if (sizeof($error))
		{
			$submit = false;
		}

		// We go through the display_vars to make sure no one is trying to set variables he/she is not allowed to...
		foreach ($display_vars['vars'] as $config_name => $null)
		{
			if (!isset($cfg_array[$config_name]) || strpos($config_name, 'legend') !== false)
			{
				continue;
			}

			$this->new_config[$config_name] = $config_value = $cfg_array[$config_name];

			if ($submit)
			{
				$config->set($config_name, $config_value);
			}
		}

		if ($submit)
		{
			// Image Watermark Start
			$s_wm_type = $request->variable('wm_img_type', array(''));
			$s_wm_type = serialize($s_wm_type);
			$config->set('wm_img_type', $s_wm_type);

			trigger_error($user->lang['CONFIG_UPDATED'] . adm_back_link($this->u_action));
		}

		$this->page_title = $display_vars['title'];

		$template->assign_vars(array(
			'L_TITLE'			=> $user->lang[$display_vars['title']],
			'L_TITLE_EXPLAIN'	=> $user->lang[$display_vars['title'] . '_EXPLAIN'],

			'S_ERROR'			=> (sizeof($error)) ? true : false,
			'ERROR_MSG'			=> implode('<br />', $error),
		));
		
		// Output relevant page
		foreach ($display_vars['vars'] as $config_key => $vars)
		{
			if (!is_array($vars) && strpos($config_key, 'legend') === false)
			{
				continue;
			}

			if (strpos($config_key, 'legend') !== false)
			{
				$template->assign_block_vars('options', array(
					'S_LEGEND'		=> true,
					'LEGEND'		=> (isset($user->lang[$vars])) ? $user->lang[$vars] : $vars)
				);

				continue;
			}

			$type = explode(':', $vars['type']);

			$l_explain = '';
			if ($vars['explain'] && isset($vars['lang_explain']))
			{
				$l_explain = (isset($user->lang[$vars['lang_explain']])) ? $user->lang[$vars['lang_explain']] : $vars['lang_explain'];
			}
			else if ($vars['explain'])
			{
				$l_explain = (isset($user->lang[$vars['lang'] . '_EXPLAIN'])) ? $user->lang[$vars['lang'] . '_EXPLAIN'] : '';
			}

			$content = build_cfg_template($type, $config_key, $this->new_config, $config_key, $vars);

			if (empty($content))
			{
				continue;
			}

			$template->assign_block_vars('options', array(
				'KEY'			=> $config_key,
				'TITLE'			=> (isset($user->lang[$vars['lang']])) ? $user->lang[$vars['lang']] : $vars['lang'],
				'S_EXPLAIN'		=> $vars['explain'],
				'TITLE_EXPLAIN'	=> $l_explain,
				'CONTENT'		=> $content,
				)
			);

			unset($display_vars['vars'][$config_key]);
		}
	}

	// Image Watermark Position
	function position_select($value, $key)
	{
		global $user, $config;

		$default = isset($config['default_position']) ? $config['default_position'] : 'center';

		$position_options = '<select id="default_position" name="config[default_position]">';
		foreach ($user->lang['position'] as $key => $value)
		{
			$position_options .= '<option value="' . $key . '"' . (($key == $default) ? ' selected="selected"' : '') . '>';
			$position_options .= $value;
			$position_options .= '</option>';
		}
		$position_options .= '</select>';

		return $position_options;
	}

	// Watermark Img Type
	function img_select_type($value, $key)
	{
		global $user, $config;

		$wm_type = array(
			'jpg' => 'jpg, jpeg',
			'gif' => 'gif',
			'png' => 'png',
		);
		$default = (!empty($config['wm_img_type'])) ? unserialize($config['wm_img_type']) : array('jpg');
		$wm_type_options = '<select id="' . $key . '" name="' . $key . '[]" multiple="multiple">';
		foreach ($wm_type as $key => $value)
		{
			$wm_type_options .= '<option value="' . $key . '"' . ((in_array($key, $default)) ? ' selected="selected"' : '') . '>';
			$wm_type_options .= $value;
			$wm_type_options .= '</option>';
		}
		$wm_type_options .= '</select>';

		return $wm_type_options;
	}
}
