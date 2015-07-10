<?php

/**
*
* @package Watermark
* @copyright 2015 Татьяна5 (c) phpbbguru.net
* @author Sheer http://www.aquaforum.lv/
* @editor Anvar http://bb3.mobi
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*/

namespace tatiana5\editor_of_attachments\core;

class watermark
{
	/** @var \phpbb\config\config */
	protected $config;

	public function __construct (\phpbb\config\config $config)
	{
		$this->config = $config;
	}

	public function watermark_images($destination_file, $type)
	{
		$wn_types = unserialize($this->config['wm_img_type']);

		if ($type == 'jpeg')
		{
			$type = 'jpg';
		}

		if (in_array($type, $wn_types))
		{
			if (preg_match('#(\x00\x21\xF9\x04.{4}\x00\x2C.*){2,}#s', file_get_contents($destination_file)))
			{
				return;
			}

			$size = getimagesize($destination_file);
			$width = $size[0];
			$height = $size[1];

			if ($this->config['watermark_min_img_size'] >= $height || $this->config['watermark_min_img_size'] >= $width)
			{
				return;
			}

			// Only use imagemagick if defined and the passthru function not disabled
			if ($type == 'jpg' || $type == 'jpeg')
			{
				$source = imagecreatefromjpeg($destination_file);
			}
			else if ($type == 'png')
			{
				$source = imagecreatefrompng($destination_file);
			}
			else if ($type == 'gif')
			{
				$source = imagecreatefromgif($destination_file);
			}

			$water_path = 'ext/tatiana5/editor_of_attachments/images/';
			if (file_exists($destination_file) && file_exists($water_path . 'watermark.png'))
			{
				$water = @imagecreatefrompng($water_path . 'watermark.png');
				if (imagesx($source) < imagesx($water)*3)
				{
					$water = imagecreatefrompng($water_path . 'watermark_med.png');
					if (imagesx($source) < imagesx($water)*3)
					{
						$water = imagecreatefrompng($water_path . 'watermark_mini.png');
						if (imagesx($source) < imagesx($water)*3)
						{
							$water = imagecreatefrompng($water_path . 'watermark_zero.png');
						}
					}
				}

				$destination = $this->create_watermark($source, $water, $this->config['watermark_opacity'], $this->config['default_position']);

				if ($type == 'jpg' || $type == 'jpeg')
				{
					imagejpeg($destination, $destination_file, $this->config['attach_img_quality']);
				}
				else if ($type == 'png')
				{
					imagepng($destination, $destination_file);
				}
				else if ($type == 'gif')
				{
					imagegif($destination, $destination_file);
				}

				//Return data
				clearstatcache();
			}
		}
	}

	public function create_watermark($main_img_obj, $watermark_img_obj, $alpha_level = 100, $position = '')
	{
		$watermark_width = imagesx($watermark_img_obj);
		$watermark_height = imagesy($watermark_img_obj);

		$image_width = imagesx($main_img_obj);
		$image_height = imagesy($main_img_obj);

		switch ($position)
		{
			case 'left_bottom':
				$x_shift = $image_width - $watermark_width - 5;
				$y_shift = 5;
				break;
			case 'right_top':
				$x_shift = 5;
				$y_shift = $image_height - $watermark_height - 5;
				break;
			case 'left_top':
				$x_shift = $image_width - $watermark_width - 5;
				$y_shift = $image_height - $watermark_height - 5;
				break;
			case 'left_center':
				$x_shift = $image_width - $watermark_width - 5;
				$y_shift = (int)($image_height / 2) - (int)($watermark_height / 2) - 5;
				break;
			case 'right_center':
				$x_shift = 5;
				$y_shift = (int)($image_height / 2) - (int)($watermark_height / 2) - 5;
				break;
			case 'right_bottom':
				$x_shift = 5;
				$y_shift = 5;
				break;
			case 'center':
			default:
				$x_shift = (int)($image_width / 2) - (int)($watermark_width / 2) - 5;
				$y_shift = (int)($image_height / 2) - (int)($watermark_height / 2) - 5;
				break;
		}

		$dest_x = imagesx($main_img_obj) - $watermark_width - $x_shift;
		$dest_y = imagesy($main_img_obj) - $watermark_height - $y_shift;
		$result = $this->imagecopymerge_alpha($main_img_obj, $watermark_img_obj, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, $alpha_level);
		if ($result)
		{
			return $main_img_obj;
		}

		return false;
	}

	private function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct)
	{
		$pct /= 100;

		$w = imagesx($src_im);
		$h = imagesy($src_im);

		imagealphablending($src_im, false);

		$minalpha = 127;
		for($x = 0; $x < $w; $x++)
		{
			for($y = 0; $y < $h; $y++)
			{
				$alpha = (imagecolorat($src_im, $x, $y) >> 24) & 0xFF;
				if($alpha < $minalpha)
				{
					$minalpha = $alpha;
				}
			}
		}

		for($x = 0; $x < $w; $x++)
		{
			for($y = 0; $y < $h; $y++)
			{
				$colorxy = imagecolorat($src_im, $x, $y);
				$alpha = ($colorxy >> 24) & 0xFF;

				if($minalpha !== 127)
				{
					$alpha = 127 + 127 * $pct * ($alpha - 127) / (127 - $minalpha);
				}
				else
				{
					$alpha += 127 * $pct;
				}

				$alphacolorxy = imagecolorallocatealpha($src_im, ($colorxy >> 16) & 0xFF, ($colorxy >> 8) & 0xFF, $colorxy & 0xFF, $alpha);

				if(!imagesetpixel($src_im, $x, $y, $alphacolorxy))
				{
					return false;
				}
			}
		}

		imagecopy($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h);
		return true;
	}
}
