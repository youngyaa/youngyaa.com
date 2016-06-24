<?php

/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2002 - 2013 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
abstract class OSMembershipHelperHtml
{
	/**
	 * Function to render a common layout which is used in different views
	 *
	 * @param string $layout
	 * @param array  $data
	 *
	 * @return string
	 * @throws RuntimeException
	 */
	public static function loadCommonLayout($layout, $data = array())
	{
		$app       = JFactory::getApplication();
		$themeFile = str_replace('/tmpl', '', $layout);
		if (JFile::exists($layout))
		{
			$path = $layout;
		}
		elseif (JFile::exists(JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_osmembership/' . $themeFile))
		{
			$path = JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_osmembership/' . $themeFile;
		}
		elseif (JFile::exists(JPATH_ROOT . '/components/com_osmembership/view/' . $layout))
		{
			$path = JPATH_ROOT . '/components/com_osmembership/view/' . $layout;
		}
		else
		{
			throw new RuntimeException(JText::_('The given shared template path is not exist'));
		}
		// Start an output buffer.
		ob_start();
		extract($data);

		// Load the layout.
		include $path;

		// Get the layout contents.
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Generate category selection dropdown
	 *
	 * @param int    $selected
	 * @param string $name
	 * @param string $attr
	 *
	 * @return mixed
	 */
	public static function buildCategoryDropdown($selected, $name = "parent_id", $attr = null)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, parent_id, title')
			->from('#__osmembership_categories')
			->where('published=1');
		$db->setQuery($query);
		$rows     = $db->loadObjectList();
		$children = array();
		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list      = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options   = array();
		$options[] = JHtml::_('select.option', '0', JText::_('OSM_SELECT_CATEGORY'));
		foreach ($list as $item)
		{
			$options[] = JHtml::_('select.option', $item->id, '&nbsp;&nbsp;&nbsp;' . $item->treename);
		}

		return JHtml::_('select.genericlist', $options, $name,
			array(
				'option.text.toHtml' => false,
				'option.text'        => 'text',
				'option.value'       => 'value',
				'list.attr'          => 'class="inputbox" ' . $attr,
				'list.select'        => $selected));
	}

	/**
	 * Converts a double colon seperated string or 2 separate strings to a string ready for bootstrap tooltips
	 *
	 * @param   string $title     The title of the tooltip (or combined '::' separated string).
	 * @param   string $content   The content to tooltip.
	 * @param   int    $translate If true will pass texts through JText.
	 * @param   int    $escape    If true will pass texts through htmlspecialchars.
	 *
	 * @return  string  The tooltip string
	 *
	 * @since   2.0.7
	 */
	public static function tooltipText($title = '', $content = '', $translate = 1, $escape = 1)
	{
		// Initialise return value.
		$result = '';

		// Don't process empty strings
		if ($content != '' || $title != '')
		{
			// Split title into title and content if the title contains '::' (old Mootools format).
			if ($content == '' && !(strpos($title, '::') === false))
			{
				list($title, $content) = explode('::', $title, 2);
			}

			// Pass texts through JText if required.
			if ($translate)
			{
				$title   = JText::_($title);
				$content = JText::_($content);
			}

			// Use only the content if no title is given.
			if ($title == '')
			{
				$result = $content;
			}
			// Use only the title, if title and text are the same.
			elseif ($title == $content)
			{
				$result = '<strong>' . $title . '</strong>';
			}
			// Use a formatted string combining the title and content.
			elseif ($content != '')
			{
				$result = '<strong>' . $title . '</strong><br />' . $content;
			}
			else
			{
				$result = $title;
			}

			// Escape everything, if required.
			if ($escape)
			{
				$result = htmlspecialchars($result);
			}
		}

		return $result;
	}

	/**
	 * Get label of the field (including tooltip)
	 *
	 * @param        $name
	 * @param        $title
	 * @param string $tooltip
	 *
	 * @return string
	 */
	public static function getFieldLabel($name, $title, $tooltip = '')
	{
		$label = '';
		$text  = $title;

		// Build the class for the label.
		$class = !empty($tooltip) ? 'hasTooltip hasTip' : '';

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $name . '-lbl" for="' . $name . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($tooltip))
		{
			$label .= ' title="' . self::tooltipText(trim($text, ':'), $tooltip, 0) . '"';
		}

		$label .= '>' . $text . '</label>';

		return $label;
	}

	/**
	 * Get bootstrapped style boolean input
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return string
	 */
	public static function getBooleanInput($name, $value)
	{
		$html = array();

		// Start the radio field output.
		$html[] = '<fieldset id="' . $name . '" class="radio btn-group btn-group-yesno">';

		// Yes Option
		$checked = ($value == 1) ? ' checked="checked"' : '';
		$html[]  = '<input type="radio" id="' . $name . '0" name="' . $name . '" value="1"' . $checked . ' />';
		$html[]  = '<label for="' . $name . '0">' . JText::_('JYES') . '</label>';

		// No Option
		$checked = ($value == 0) ? ' checked="checked"' : '';
		$html[]  = '<input type="radio" id="' . $name . '1" name="' . $name . '" value="0"' . $checked . ' />';
		$html[]  = '<label for="' . $name . '1">' . JText::_('JNO') . '</label>';

		// End the radio field output.
		$html[] = '</fieldset>';

		return implode($html);
	}

	/**
	 * Function to add dropdown menu
	 *
	 * @param string $vName
	 */
	public static function renderSubmenu($vName = 'dashboard')
	{
	?>
	<script language="javascript">
		function confirmBuildTaxRules()
		{
			if (confirm('This will delete all tax rules you created and build EU tax rules. Are you sure ?'))
			{
				location.href = 'index.php?option=com_osmembership&task=build_eu_tax_rules';
			}
		}
	</script>
	<?php
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__osmembership_menus')
			->where('published = 1')
			->where('menu_parent_id = 0')
			->order('ordering');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		$html  = '';
		$html .= '<ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover">';

		$currentLink = 'index.php' . JUri::getInstance()->toString(array('query'));
		for ($i = 0; $n = count($menus), $i < $n; $i++)
		{
			$menu = $menus[$i];
			$query->clear();
			$query->select('*')
				->from('#__osmembership_menus')
				->where('published = 1')
				->where('menu_parent_id = ' . intval($menu->id))
				->order('ordering');
			$db->setQuery($query);
			$subMenus = $db->loadObjectList();
			if (!count($subMenus))
			{
				$class = '';
				if ($menu->menu_link == $currentLink)
				{
					$class = ' class="active"';
				}
				$html .= '<li' . $class . '><a href="' . $menu->menu_link . '"><span class="icon-' . $menu->menu_class . '"></span> ' . JText::_($menu->menu_name) .
					'</a></li>';
			}
			else
			{
				$class = ' class="dropdown"';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					if ($subMenu->menu_link == $currentLink)
					{
						$class = ' class="dropdown active"';
						break;
					}
				}
				$html .= '<li' . $class . '>';
				$html .= '<a id="drop_' . $menu->id . '" href="#" data-toggle="dropdown" role="button" class="dropdown-toggle"><span class="icon-' . $menu->menu_class . '"></span> ' .
					JText::_($menu->menu_name) . ' <b class="caret"></b></a>';
				$html .= '<ul aria-labelledby="drop_' . $menu->id . '" role="menu" class="dropdown-menu" id="menu_' . $menu->id . '">';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$class   = '';
					if ($subMenu->menu_link == $currentLink)
					{
						$class = ' class="active"';
					}
					$html .= '<li' . $class . '><a href="' . $subMenu->menu_link .
						'" tabindex="-1"><span class="icon-' . $subMenu->menu_class . '"></span> ' . JText::_($subMenu->menu_name) . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</li>';
			}
		}
		$html .= '</ul>';
		if (version_compare(JVERSION, '3.0', 'le'))
		{
			JFactory::getDocument()->setBuffer($html, array('type' => 'modules', 'name' => 'submenu'));
		}
		else
		{
			echo $html;
		}		
	}
}