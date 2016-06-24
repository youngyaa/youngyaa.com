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

class HelperBetterPreviewButtonForm2ContentForm extends HelperBetterPreviewButton
{
	function getExtraJavaScript($text)
	{
		return '
				cat = document.getElementById("jform_catid");
				category_title = cat == undefined ? "" : cat.options[cat.selectedIndex].text.replace(/^(\s*-\s+)*/, "").trim();
				overrides = {
						category_title: category_title,
					};
			';
	}

	function getURL($name)
	{
		$helper = new HelperBetterPreviewHelperForm2ContentForm($this->params);

		if (!$item = $helper->getArticle())
		{
			return;
		}

		return $item->url;
	}
}
