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
	
	public function __construct(\phpbb\auth\auth $auth, \phpbb\config\config $config, \phpbb\template\template $template, \phpbb\user $user, \phpbb\db\driver\driver_interface $db, $phpbb_root_path, $php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->template = $template;
		$this->user = $user;
		$this->db = $db;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
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
	
	public function upload_image_resizer($event) {
		if (isset($this->config['allow_attach_resize']) && $this->config['allow_attach_resize'])
		{
			$is_image = $event['is_image'];
			$filedata = $event['filedata'];
			$destination_file = $this->phpbb_root_path . $this->config['upload_path'] . '/' . $filedata['physical_filename'];
			
			if ($is_image)
			{
				$limit_width =	(isset($this->config['attach_resize_width']) && $this->config['attach_resize_width']) ? $this->config['attach_resize_width'] : 1024;
				$limit_height =	(isset($this->config['attach_resize_height']) && $this->config['attach_resize_height']) ? $this->config['attach_resize_height'] : 1024;
				$quality = 100;

				$size = getimagesize($destination_file);
				$width = $size[0];
				$height = $size[1];
				if($height > $limit_height || $width > $limit_width)
				{
					$int_factor = min(($limit_width / $width), ($limit_height / $height));
					$resize_width = round($width * $int_factor);
					$resize_height = round($height * $int_factor);

					// Only use imagemagick if defined and the passthru function not disabled
					if ($this->config['img_imagick'] && function_exists('passthru'))
					{
						$quality = '';
						$sharpen = '';
						$frame = '';
						$animation = '';
						if ($filedata['extension'] == "jpg" || $filedata['extension'] == "jpeg" )
						{
							$quality = '-quality 80'; // 80%
							/** Reduction in linear dimensions below which sharpening will be enabled */
							if (($resize_width + $resize_height ) / ($width + $height) < 0.85)
							{
								$sharpen = '-sharpen 0x0.4';
							}
						}
						elseif ($filedata['extension'] == "png")
						{
							$quality = '-quality 95'; // zlib 9, adaptive filtering
						}
						elseif ($filedata['extension'] == "gif")
						{
							/**
							* Force thumbnailing of animated GIFs above this size to a single
							* frame instead of an animated thumbnail. ImageMagick seems to
							* get real unhappy and doesn't play well with resource limits. :P
							* Defaulting to 1 megapixel (1000x1000)
							*/
							if($width * $height > 1.0e6)
							{
								// Extract initial frame only
								$frame = '[0]';
							}
							else
							{
								// Coalesce is needed to scale animated GIFs properly (MediaWiki bug 1017).
								$animation = ' -coalesce ';
							}
						}

						# Specify white background color, will be used for transparent images
						# in Internet Explorer/Windows instead of default black.

						# Note, we specify "-size {$this->width}" and NOT "-size {$this->width}x{$this->height}".
						# It seems that ImageMagick has a bug wherein it produces thumbnails of
						# the wrong size in the second case.

						if (substr($this->config['img_imagick'], -1) !== '/')
						{
							$this->config['img_imagick'] .= '/';
						}
						$cmd = escapeshellcmd($this->config['img_imagick']) . 'convert' . ((defined('PHP_OS') && preg_match('#^win#i', PHP_OS)) ? '.exe' : '') .
							" {$quality} -background white -size {$width} ".
							escapeshellarg($destination_file . $frame) .
							$animation .
						// For the -resize option a "!" is needed to force exact size,
						// or ImageMagick may decide your ratio is wrong and slice off a pixel.
						' -thumbnail ' . escapeshellarg( "{$resize_width}x{$resize_height}!" ) .
						" -depth 8 $sharpen " .
						escapeshellarg($destination_file) . ' 2>&1';

						@passthru($cmd);
						
						//Return data
						clearstatcache();
						$filedata['filesize'] = filesize($destination_file);
						$event['filedata'] = $filedata;
					}
					else if (extension_loaded('gd'))
					{
						$destination = imagecreatetruecolor($resize_width, $resize_height);

						if ($filedata['extension'] == 'jpg' || $filedata['extension'] == 'jpeg')
						{
							@ini_set('gd.jpeg_ignore_warning', true);
							$source = imagecreatefromjpeg($destination_file);
						}
						elseif ($filedata['extension'] == 'png')
						{
							@imagealphablending($destination, false);
							@imagesavealpha($destination, true);
							$source = imagecreatefrompng($destination_file);
						}
						elseif ($filedata['extension'] == 'gif')
						{
							$source = imagecreatefromgif($destination_file);
							$trnprt_indx = imagecolortransparent($source);
							if ($trnprt_indx >= 0) //transparent
							{
								$trnprt_color = imagecolorsforindex($source, $trnprt_indx);
								$trnprt_indx = imagecolorallocate($destination, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
								imagefill($destination, 0, 0, $trnprt_indx);
								imagecolortransparent($destination, $trnprt_indx);
							}
						}

						imagecopyresampled($destination, $source, 0, 0, 0, 0, $resize_width, $resize_height, $size[0], $size[1]);
						if ($filedata['extension'] == 'jpg' || $filedata['extension'] == 'jpeg') imagejpeg($destination, $destination_file, $quality);
						elseif ($filedata['extension'] == 'png')
						{
							imagepng($destination, $destination_file);
						}
						elseif ($filedata['extension'] == 'gif')
						{
							imagegif($destination, $destination_file);
						}
						imagedestroy($destination);
						imagedestroy($source);

						//Return data
						clearstatcache();
						$filedata['filesize'] = filesize($destination_file);
						$event['filedata'] = $filedata;
					}
				}
			}
		}
	}
	
	public function quote_of_attachments($event) {
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
				
					$sql_attach = 'SELECT attach_id, real_filename, mimetype
										FROM ' . ATTACHMENTS_TABLE . ' 
										WHERE post_msg_id = ' . $post_id;
					$result_attach = $this->db->sql_query($sql_attach);
					while ($attach_row = $this->db->sql_fetchrow($result_attach))
					{
						if(in_array($attach_row['real_filename'], $attach_in_quote[1]))
						{
							//Replace inline attachments
							if(strpos($attach_row['mimetype'], 'image/') !== false)
							{
								//Replase image inline attachments in [img]
								$message_parser->message = preg_replace('/\[attachment=\d+\]' . preg_quote($attach_row['real_filename']) . '\[\/attachment\]/', $img_open_tag . generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . $img_close_tag, $message_parser->message);
							}
							else
							{
								//Replase other inline attachments in [url]
								if($allow_url)
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
							if(strpos($attach_row['mimetype'], 'image/') !== false)
							{
								//Replace image attachments in [img]
								$message_parser->message .= "\n" . $img_open_tag . generate_board_url() . '/download/file.php?id=' . (int) $attach_row['attach_id'] . $img_close_tag;
							}
							else
							{
								//Replace other attachments in [url]
								if($allow_url)
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
					
					$page_data = array_merge($page_data, array('MESSAGE'	=> $post_data['post_text']));
					$event['page_data'] = $page_data; //Return data
				}
			}
		}
	}
}
