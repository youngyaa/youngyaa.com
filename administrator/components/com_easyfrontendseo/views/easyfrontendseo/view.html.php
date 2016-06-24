<?php
/**
 * EFSEO - Easy Frontend SEO for Joomal! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Project page: https://joomla-extensions.kubik-rubik.de/efseo-easy-frontend-seo
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') or die('Restricted access');

class EasyFrontendSeoViewEasyFrontendSeo extends JViewLegacy
{
    protected $state;

    function display($tpl = null)
    {
        $bar = JToolBar::getInstance('toolbar');

        JToolBarHelper::title(JText::_('COM_EASYFRONTENDSEO')." - ".JText::_('COM_EASYFRONTENDSEO_SUBMENU_ENTRIES'), 'easyfrontendseo');
        JToolBarHelper::addNew();
        JToolBarHelper::deleteList();
        JToolBarHelper::editList();

        $layout = new JLayoutFile('joomla.toolbar.batch');
        $bar->appendButton('Custom', $layout->render(array('title' =>  JText::_('JTOOLBAR_BATCH'))), 'batch');

        JToolBarHelper::preferences('com_easyfrontendseo', '500');

        $this->items = $this->get('Data');
        $this->pagination = $this->get('Pagination');
        $this->plugin_state = $this->get('PluginStatus');
        $this->state = $this->get('State');

        $document = JFactory::getDocument();
        $document->addStyleSheet('components/com_easyfrontendseo/css/easyfrontendseo.css');

        // Get donation code message
        require_once JPATH_COMPONENT.'/helpers/easyfrontendseo.php';
        $donation_code_message = EasyFrontendSeoHelper::getDonationCodeMessage();
        $this->donation_code_message = $donation_code_message;

        parent::display($tpl);
    }

}
