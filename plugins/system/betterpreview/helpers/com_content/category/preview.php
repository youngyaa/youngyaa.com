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

class HelperBetterPreviewPreviewContentCategory extends HelperBetterPreviewPreview
{

	function renderPreview(&$article, $context)
	{
		if ($context != 'com_content.category' || isset($article->introtext))
		{
			return;
		}
		parent::renderPreview($article, $context);
	}

	function states()
	{
		parent::initStates(
			'categories',
			array('parent' => 'parent_id'),
			'categories',
			array('parent' => 'parent_id')
		);
	}
}
