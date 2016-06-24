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

class HelperBetterPreviewPreviewContentArticle extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_content.article' || !isset($article->id) || $article->id != JFactory::getApplication()->input->get('id'))
		{
			return;
		}

		parent::renderPreview($article, $context);
	}

	function states()
	{
		parent::initStates(
			'content',
			array(
				'published'    => 'state',
				'publish_up'   => 'publish_up',
				'publish_down' => 'publish_down',
				'parent'       => 'catid',
				'hits'         => 'hits',
			),
			'categories',
			array(
				'parent' => 'parent_id',
			)
		);
	}
}
