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

class HelperBetterPreviewLinkK2Item extends HelperBetterPreviewLink
{
	function getLinks()
	{
		$helper = new HelperBetterPreviewHelperK2Item($this->params);

		if (!$item = $helper->getK2Item())
		{
			return;
		}

		$parents = $helper->getK2ItemParents($item);

		return array_merge(array($item), $parents);
	}
}
