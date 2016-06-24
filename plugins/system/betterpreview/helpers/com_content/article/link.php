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

class HelperBetterPreviewLinkContentArticle extends HelperBetterPreviewLink
{
	function getLinks()
	{
		$helper = new HelperBetterPreviewHelperContentArticle($this->params);

		if (!$item = $helper->getArticle())
		{
			return;
		}

		$parents = $helper->getArticleParents($item);

		return array_merge(array($item), $parents);
	}
}
