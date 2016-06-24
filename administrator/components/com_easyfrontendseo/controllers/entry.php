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

class EasyFrontendSeoControllerEntry extends JControllerLegacy
{
    protected $input;

    function __construct()
    {
        parent::__construct();

        $this->registerTask('add', 'edit');
        $this->registerTask('apply', 'save');
        $this->input = JFactory::getApplication()->input;
    }

    /**
     * Loads the edit view
     */
    function edit()
    {
        $this->input->set('view', 'entry');
        $this->input->set('layout', 'form');
        $this->input->set('hidemainmenu', 1);
        parent::display();
    }

    /**
     * Saves the entered data to the database and redirects the request correctly
     */
    function save()
    {
        JSession::checkToken() OR jexit('Invalid Token');

        $model = $this->getModel('entry');

        if($model->store())
        {
            $msg = JText::_('COM_EASYFRONTENDSEO_ENTRY_SAVED');
            $type = 'message';
        }
        else
        {
            // Save the entered data to avoid loss
            $model->storeInputSession($this->input);

            if($model->getError() == 'duplicate')
            {
                $msg = JText::_('COM_EASYFRONTENDSEO_ERROR_SAVING_ENTRY_DUPLICATE');
            }
            else
            {
                $msg = JText::_('COM_EASYFRONTENDSEO_ERROR_SAVING_ENTRY');
            }

            $type = 'error';

            // If an error occurred, then always redirect back to the edit form
            if(!$model->getId())
            {
                $this->input->set('url_current', 'option=com_easyfrontendseo&controller=entry&task=edit');
                $this->setRedirect('index.php?'.$this->input->getString('url_current'), $msg, $type);
            }
            else
            {
                $this->setRedirect('index.php?'.$this->input->getString('url_current'), $msg, $type);
            }

            return;
        }

        if($this->task == 'apply')
        {
            $this->setRedirect('index.php?'.$this->input->getString('url_current'), $msg, $type);
        }
        else
        {
            $this->setRedirect('index.php?option=com_easyfrontendseo', $msg, $type);
        }
    }

    /**
     * Deletes selected entries from the database
     */
    function remove()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('entry');

        if(!$model->delete())
        {
            $msg = JText::_('COM_EASYFRONTENDSEO_ERROR_ENTRY_COULD_NOT_BE_DELETED');
            $type = 'error';
        }
        else
        {
            $msg = JText::_('COM_EASYFRONTENDSEO_ENTRY_DELETED');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easyfrontendseo', false), $msg, $type);
    }

    /**
     * Aborts an operation and redirects to the main view
     */
    function cancel()
    {
        $msg = JText::_('COM_EASYFRONTENDSEO_OPERATION_CANCELLED');
        $this->setRedirect('index.php?option=com_easyfrontendseo', $msg, 'notice');
    }

    /**
     * Executes the batch method
     */
    public function batch()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $model = $this->getModel('entry');

        if(!$model->batch())
        {
            $msg = JText::_('COM_EASYFRONTENDSEO_ERROR_BATCH_SAVE_PROCESS');
            $type = 'error';
        }
        else
        {
            $msg = JText::_('COM_EASYFRONTENDSEO_SUCCESS_BATCH_SAVE_PROCESS');
            $type = 'message';
        }

        $this->setRedirect(JRoute::_('index.php?option=com_easyfrontendseo', false), $msg, $type);
    }
}
