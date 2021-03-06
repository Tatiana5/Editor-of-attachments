<?php
/**
*
* @package editor_of_attachments
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\editor_of_attachments\migrations\v1xx;

class v_1_0_0 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['editor_attach_version']) && version_compare($this->config['editor_attach_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_schema()
	{
		return array(
		);
	}

	public function revert_schema()
	{
		return array(
		);
	}

	public function update_data()
	{
		return array(
			// Add configs
			array('config.add', array('allow_quote_attach', '1')),

			array('config.add', array('attach_img_quality', '90')),
			array('config.add', array('img_create_watermark', '0')),
			//array('config.add', array('user_confirm_watermark', '0')),
			array('config.add', array('watermark_opacity', '90')),
			array('config.add', array('watermark_min_img_size', '200')),
			array('config.add', array('default_position', '')),

			// Current version
			array('config.add', array('editor_attach_version', '1.0.0')),

			// Add ACP modules
			array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_EDITOR_OF_ATTACHMENTS')),
			array('module.add', array('acp', 'ACP_EDITOR_OF_ATTACHMENTS', array(
					'module_basename'	=> '\tatiana5\editor_of_attachments\acp\editor_of_attachments_module',
					'module_langname'	=> 'ACP_EDITOR_OF_ATTACHMENTS_EXPLAIN',
					'module_mode'		=> 'config_editor_of_attachments',
					'module_auth'		=> 'ext_tatiana5/editor_of_attachments && acl_a_editor_attach',
			))),
			
			// Add permissions
			array('permission.add', array('a_editor_attach', true)),

			// Set permissions
			array('permission.permission_set', array('ROLE_ADMIN_FULL', 'a_editor_attach')),
		);
	}
}