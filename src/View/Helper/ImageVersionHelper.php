<?php
/**
 * Image Version Helper class to embed thumbnail images on a page.
 *
 * @link			http://www.concepthue.com
 * @author			Tom Maiaroto
 * @modifiedby		Gonzalo Balabasquer
 * @lastmodified	2008-10-04 16:11:00
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */

namespace ImageVersion\View\Helper;

use Cake\Controller\ComponentRegistry;
use Cake\View\Helper;
use ImageVersion\Controller\Component\ImageVersionComponent;

class ImageVersionHelper extends Helper
{

	public $helpers = ['Html'];

	/**
	 * Returns a block of HTML code that embeds a thumbnail image into a page.
	 * It uses the built in CakePHP HTML helper image method for additional options.
	 *
	 * @param $image String[required] Location of the source image.
	 * @param $size Array[optional] Size of the thumbnail. Default: 75x75
	 * @param $thumbQuality Int[optional] Quality of the thumbnail. Default: 85%
	 * @param $options Object[optional] An array of options, same as Html->image() helper.
	 *
	 * @return HTML string including image tag and src attribute, along with any additional options.
	 */
	function version($image=null, $size=array(75, 75), $thumbQuality=85, $crop=false, $options=array(), $folderData='', $returnUrl = false) {
		
		if(substr($image, 0, 4) == 'http') {
			$options = array();
			if(isset($size[0])) {
				$options['width'] = $size[0];
			}
			if(isset($size[1])) {
				$options['height'] = $size[1];
			}
			return $this->Html->image($image, $options);
		}
		
		// init the component, if it hasn't been initialized
		if(!$this->component):
			$this->component = new ImageVersionComponent(new ComponentRegistry());
		endif;

		$outputImage = str_replace("\\","/",$this->component->version($image, $size, $thumbQuality, $crop, $folderData));

		if($returnUrl){
            if(!empty($folderData)):
                return '..'.str_replace("/".APP_DIR."/", "/", "/".$outputImage);
            else:
                return "..".$outputImage;
            endif;
        }

		if(!empty($folderData)):
			$link = str_replace("/".APP_DIR."/", "/", $this->Html->image("/".$outputImage, $options));
		else:
			$link = $this->Html->image("..".$outputImage, $options);
		endif;

        $link = str_replace("//","/", $link);
		
		//return $this->output("$link");
		return $link;
	}

	/**
	* Deletes a single version thumbnail and/or deletes the entire directory of versions.
	*
	* @param $source String[required] Location of the source image.
	* @param $size Array[optional] Image version.
	* @param $clearAll Boolean[optional] Specify whether or not to remove all versions in a folder.
	* @return
	*/
	function flushVersion($source=null, $size=array(75,75), $clearAll=false) {
		if((is_null($source)) || (!is_string($source))): return false; endif;
		// init the component, if it hasn't been initialized
		if(!$this->component):
			$this->component = new ImageVersionComponent;
		endif;
		$flush = $this->component->flushVersion($source, $size, $clearAll);
		return;
	}
}
?>
