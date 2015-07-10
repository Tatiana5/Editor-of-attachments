<?php
/**
*
* @package editor_of_attachments
* @copyright (c) 2014 Татьяна5
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace tatiana5\editor_of_attachments\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class listener implements EventSubscriberInterface
{	
	/** @var \phpbb\auth\auth */
	protected $auth;
	
	/** @var \phpbb\config\config */
	protected $config;
	
	/** @var \phpbb\template\template */
	protected $template;
	
	/** @var \phpbb\user */
	protected $user;
	
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/** @var string */
	protected $phpbb_root_path;
	protected $php_ext;

	/** @var watermark */
	protected $watermark;
	/**
	* Constructor
	* 
	* @param \phpbb\auth\auth $auth
	* @param \phpbb\config\config $config
	* @param \phpbb\template\template $template
	* @param \phpbb\user $user
	* @param \phpbb\db\driver\driver_interface $db
	* @param string $phpbb_root_path Root path
	* @param string $php_ext
	*/
	
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext, $watermark)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;

		/** Class watermark */
		$this->watermark = $watermark;
	}
	
	/**
	* Assign functions defined in this class to event listeners in the core
	*
	* @return array
	* @static
	* @access public
	*/
	static public function getSubscribedEvents()
	{
		return array(
			'core.modify_uploaded_file'			=> 'upload_image_resizer',
			'core.posting_modify_template_vars'	=> 'quote_of_attachments',
		);
	}

	public function upload_image_resizer($event)
	{
		if ($this->config['img_create_watermark'])
		{
			$is_image = $event['is_image'];
			$filedata = $event['filedata'];
			$destination_file = $this->phpbb_root_path . $this->config['upload_path'] . '/' . $filedata['physical_filename'];
			if ($is_image && file_exists($destination_file))
			{
				$this->watermark->watermark_images($destination_file, $filedata['extension']);
			}
		}
	}

	public function quote_of_attachments($event)
	{
		if (isset($this->config['allow_quote_attach']) && $this->config['allow_quote_attach'])
		{
			//Get data
			$post_data = $event['post_data'];
			$mode = $event['mode'];
			$post_id = $event['post_id'];
			$forum_id = $event['forum_id'];
			$submit = $event['submit'];
			$preview = $event['preview'];
			$refresh = $event['refresh'];
			$page_data = $event['page_data'];
			$message_parser = $event['message_parser'];
			
			if ($mode == 'quote' && !$submit && !$preview && !$refresh)
			{
				if ($this->config['allow_bbcode'])
				{
					//Permission checks
					$allow_url = ($this->config['allow_post_links'] && $this->auth->acl_get('f_bbcode', $forum_id)) ? true : false;

					$img_open_tag = ($this->auth->acl_get('f_bbcode', $forum_id) && $this->auth->acl_get('f_img', $forum_id)) ? '[img]' : ' ';
					$img_close_tag = ($this->auth->acl_get('f_bbcode', $forum_id) && $this->auth->acl_get('f_img', $forum_id)) ? '[/img]' : ' ';

					//Replacement
					$attach_in_quote = array();
					$message_parser->message = substr($message_parser->message, 0, strlen($message_parser->message) - 9); //Del "[/quote]\n"
					preg_match_all('/\[attachment=\d+\](.*)\[\/attachment\]/U', $message_parser->message, $attach_in_quote);

					$sql_attach = 'SELECT attach_id, real_filename, mimetype, extension, thumbnail
						FROM ' . ATTACHMENTS_TABLE . ' 
							WHERE post_msg_id = ' . $post_id;
					$result_attach = $this->db->sql_query($sql_attach);
					while ($attach_row = $this->db->sql_fetchrow($result_attach))
					{
						if (in_array($attach_row['real_filename'], $attach_in_quote[1]))
						{
							//Replace inline attachments
							if (strpos($attach_row['mimetype'], 'image/') !== false)
							{
								if (!empty($this->config['seoimg_version']))
								{
									$type_link = ($attach_row['thumbnail']) ? 'thumb' : 'small';
									$img_link = generate_board_url() ."/{$type_link}/{$attach_row['attach_id']}.{$attach_row['extension']}";
								}
								else
								{
									$type_link = ($attach_row['thumbnail']) ? '&t=1' : '';
									$img_link = generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . $type_link;
								}
								//Replase image inline attachments in [img]
								$message_parser->message = preg_replace('/\[attachment=\d+\]' . preg_quote($attach_row['real_filename']) . '\[\/attachment\]/', $img_open_tag . $img_link . $img_close_tag, $message_parser->message);
							}
							else
							{
								//Replase other inline attachments in [url]
								if ($allow_url)
								{
									$message_parser->message = preg_replace('/\[attachment=\d+\]' . preg_quote($attach_row['real_filename']) . '\[\/attachment\]/', '[url=' . generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . ']' . $attach_row['real_filename'] . '[/url]', $message_parser->message);
								}
								else
								{
									$message_parser->message = preg_replace('/\[attachment=\d+\]' . preg_quote($attach_row['real_filename']) . '\[\/attachment\]/', generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'], $message_parser->message);
								}
							}

							//Fix if there the same filenames
							$key_attach = array_search($attach_row['real_filename'], $attach_in_quote[1]);
							if ($key_attach !== false) 
							{
								unset($attach_in_quote[1][$key_attach]);
							}
						}
						else
						{
							//Replace (not-inline) attachments
							if (strpos($attach_row['mimetype'], 'image/') !== false)
							{
								if (!empty($this->config['seoimg_version']))
								{
									$type_link = ($attach_row['thumbnail']) ? 'thumb' : 'small';
									$img_link = generate_board_url() ."/{$type_link}/{$attach_row['attach_id']}.{$attach_row['extension']}";
								}
								else
								{
									$type_link = ($attach_row['thumbnail']) ? '&t=1' : '';
									$img_link = generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . $type_link;
								}
								//Replace image attachments in [img]
								$message_parser->message .= "\n" . $img_open_tag . $img_link . $img_close_tag;
							}
							else
							{
								//Replace other attachments in [url]
								if ($allow_url)
								{
									$message_parser->message .= "\n[url=" . generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . ']' . $attach_row['real_filename'] . '[/url]';
								}
								else
								{
									$message_parser->message .= "\n" . generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . ' ';
								}
							}
						}
					}
					$this->db->sql_freeresult($result_attach);
					unset($attach_row);
					$message_parser->message .= "[/quote]\n";
					$post_data['post_text'] = $message_parser->message;

					$page_data = array_merge($page_data, array('MESSAGE' => $post_data['post_text']));
					$event['page_data'] = $page_data; //Return data
				}
			}
		}
	}
}
