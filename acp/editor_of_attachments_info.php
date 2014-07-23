<?php
/**
*
* @package editor_of_attachments
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

namespace tatiana5\editor_of_attachments\acp;

class editor_of_attachments_info
{
	function module()
	{
		return array(
			'filename'	=> '\tatiana5\editor_of_attachments\acp\editor_of_attachments_module',
			'title'		=> 'ACP_EDITOR_OF_ATTACHMENTS',
			'version'	=> '0.0.1',
			'modes'		=> array(
				'config_editor_of_attachments'		=> array('title' => 'ACP_EDITOR_OF_ATTACHMENTS', 'auth' => 'acl_a_editor_attach', 'cat' => array('ACP_EDITOR_OF_ATTACHMENTS')),
			),
		);
	}
}

?>