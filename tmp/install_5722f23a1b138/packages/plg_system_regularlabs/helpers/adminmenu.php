<?php
/**
 * @package         Regular Labs Library
 * @version         16.4.23089
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2016 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

class PlgSystemRegularLabsAdminMenuHelper
{
	public function combine()
	{
		$html = JFactory::getApplication()->getBody();

		if ($html == '')
		{
			return;
		}

		if (strpos($html, '<ul id="menu"') === false
			|| strpos($html, '">Regular Labs ') === false
		)
		{
			return;
		}

		if (!preg_match_all('#<li><a class="menu-[^>]*>Regular Labs [^<]*</a></li>#si', $html, $matches))
		{
			return;
		}

		$menu_items = $matches['0'];

		if (count($menu_items) < 2)
		{
			return;
		}

		$manager = null;

		foreach ($menu_items as $i => &$menu_item)
		{
			preg_match('#class="menu-(.*?)"#s', $menu_item, $icon);

			$menu_item = str_replace(
				array('>Regular Labs - ', '>Regular Labs '),
				'><span class="icon-reglab icon-' . $icon['1'] . '"></span> ',
				$menu_item
			);

			if ($icon['1'] != 'regularlabsmanager')
			{
				continue;
			}

			$manager = $menu_item;
			unset($menu_items[$i]);
		}

		$main_link = "";

		if (!is_null($manager))
		{
			array_unshift($menu_items, $manager);
			$main_link = 'href="index.php?option=com_regularlabsmanager"';
		}

		$new_menu_item =
			'<li class="dropdown-submenu">'
			. '<a class="dropdown-toggle menu-regularlabs" data-toggle="dropdown" ' . $main_link . '>Regular Labs</a>'
			. '<ul id="menu-cregularlabs" class="dropdown-menu menu-component">'
			. implode('', $menu_items)
			. '</ul>'
			. '</li>';

		$first = array_shift($matches['0']);

		$html = str_replace($first, $new_menu_item, $html);
		$html = str_replace($matches['0'], '', $html);

		JFactory::getApplication()->setBody($html);
	}
}

