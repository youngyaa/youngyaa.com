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

class HelperBetterPreviewButtonZooCategoryEdit extends HelperBetterPreviewButton
{
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

		$item = $zoo->table->category->get($id);

		return $zoo->route->category($item, 0);
	}
}
