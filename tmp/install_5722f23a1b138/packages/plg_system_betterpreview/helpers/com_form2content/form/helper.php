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

include_once JPATH_SITE . '/components/com_content/helpers/route.php';

class HelperBetterPreviewHelperForm2ContentForm extends PlgSystemBetterPreviewHelper
{
	function getArticle()
	{
		if (JFactory::getApplication()->input->get('layout', 'edit') != 'edit'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return;
		}

		$this->q->clear()
			->select('c.reference_id')
			->from('#__f2c_form AS c')
			->where('c.id = ' . (int) JFactory::getApplication()->input->get('id'));
		$this->db->setQuery($this->q);
		$article_id = $this->db->loadResult();

		$item = $this->getItem(
			$article_id,
			'content',
			array('name' => 'title', 'published' => 'state', 'language' => 'language', 'parent' => 'catid'),
			array('type' => 'RL_ARTICLE')
		);

		$item->url = ContentHelperRoute::getArticleRoute($item->id, $item->parent, $item->language);

		return $item;
	}

	function getArticleParents($item)
	{
		if (empty($item)
			|| JFactory::getApplication()->input->get('layout', 'edit') != 'edit'
			|| !JFactory::getApplication()->input->get('id')
		)
		{
			return false;
		}

		$parents = $this->getParents(
			$item,
			'categories',
			array('name' => 'title', 'parent' => 'parent_id', 'language' => 'language'),
			array('type' => 'JCATEGORY'),
			1
		);

		foreach ($parents as &$parent)
		{
			$parent->url = ContentHelperRoute::getCategoryRoute($parent->id, $item->language);
		}

		return $parents;
	}
}
