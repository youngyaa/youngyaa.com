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

/**
 ** Plugin that places the button
 */
class HelperBetterPreviewButton extends PlgSystemBetterPreviewHelper
{
	public function __construct(&$params)
	{
		parent::__construct($params);
	}

	function getExtraJavaScript($text)
	{
		return '';
	}
}
