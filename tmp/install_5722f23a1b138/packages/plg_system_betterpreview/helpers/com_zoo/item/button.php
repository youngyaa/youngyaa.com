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

class HelperBetterPreviewButtonZooitem extends HelperBetterPreviewButton
{
	function getExtraJavaScript($text)
	{
		return '
				isjform = 0;
			';
	}

	function getURL($name)
	{
		$id = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$id = (int) $id[0];

		if (!$id)
		{
			return;
		}

		require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

		$zoo = App::getInstance('zoo');

		$item = $zoo->table->item->get($id);

		$url = $zoo->route->item($item, 0);

		if (strpos($url, 'item_id=') !== false)
		{
			return $url;
		}

		return $url . '&item_id=' . $id;
	}
}
