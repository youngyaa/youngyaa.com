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

class HelperBetterPreviewPreviewZooItemlist extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_zoo.category.description')
		{
			return;
		}
		parent::renderPreview($article, $context);
	}

	function states()
	{
		require_once(JPATH_ADMINISTRATOR . '/components/com_zoo/config.php');

		$zoo = App::getInstance('zoo');

		$id = JFactory::getApplication()->input->get('category_id');

		$cat = $zoo->table->category->get($id);
		while ($cat)
		{
			$this->states[] = (object) array(
				'table'     => 'zoo_category',
				'id'        => $cat->id,
				'name'      => $cat->name,
				'published' => $cat->published,
				'url'       => $zoo->route->category($cat, 0),
				'type'      => JText::_('CATEGORY'),
				'names'     => (object) array(
					'id'        => 'id',
					'published' => 'published',
				),
			);
			$cat            = $cat->parent ? $zoo->table->category->get($cat->parent) : 0;
		}

		$this->setStates();
	}

	function getShowIntro(&$article)
	{
		return 1;
	}
}
