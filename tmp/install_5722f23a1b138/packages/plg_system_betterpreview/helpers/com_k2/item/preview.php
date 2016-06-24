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

class HelperBetterPreviewPreviewK2item extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_k2.item' || !isset($article->id) || $article->id != JFactory::getApplication()->input->get('id'))
		{
			return;
		}
		parent::renderPreview($article, $context);
	}

	function states()
	{
		parent::initStates(
			'k2_items',
			array(
				'publish_up'   => 'publish_up',
				'publish_down' => 'publish_down',
				'parent'       => 'catid',
			),
			'k2_categories',
			array()
		);
	}

	function getShowIntro(&$article)
	{
		if (!isset($article->params))
		{
			return 1;
		}

		if (!is_object($article->params))
		{
			$params = (object) json_decode($article->params);

			return $params->itemIntroText;
		}

		return $article->params->get('itemIntroText', '1');
	}
}
