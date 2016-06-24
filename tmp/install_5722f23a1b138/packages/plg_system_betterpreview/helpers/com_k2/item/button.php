<?php
/**
 * @package         Better Preview
 * @version         5.0.1
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

include_once __DIR__ . '/helper.php';

class HelperBetterPreviewButtonK2Item extends HelperBetterPreviewButton
{
	function getExtraJavaScript($text)
	{
		return '
				isjform = 0;
				text = text.split(\'<hr id="system-readmore">\');
				introtext = text[0];
				fulltext =  text[1] == undefined ? "" : text[1];
				text = (introtext + " " + fulltext).trim();
				overrides = {
						text: text,
						introtext: introtext,
						fulltext: fulltext,
					};
			';
	}

	function getURL($name)
	{
		$helper = new HelperBetterPreviewHelperK2Item($this->params);

		if (!$item = $helper->getK2Item())
		{
			return;
		}

		return $item->url;
	}
}
