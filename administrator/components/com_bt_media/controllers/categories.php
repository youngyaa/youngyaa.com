<?php

/**
 * @package     com_bt_media - BT Media
 * @version	1.1.0
 * @created	Oct 2012
 * @author	BowThemes
 * @email	support@bowthems.com
 * @website	http://bowthemes.com
 * @support	Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license	http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Categorys list controller class.
 */
class Bt_mediaControllerCategories extends JControllerAdmin {

    /**
     * Proxy for getModel.
     * @since	1.6
     */
    public function getModel($name = 'category', $prefix = 'Bt_mediaModel', $config = array('ignore_request' => true)) {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * Save the manual order inputs from the categories list page.
     *
     * @return  void
     * @since   1.6
     */
    public function saveorder() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get the arrays from the Request
        $order = JFactory::getApplication()->input->post->get('order', null, 'array');
        $originalOrder = explode(',', JFactory::getApplication()->input->getString('original_order_values'));

        // Make sure something has changed
        if (!($order === $originalOrder)) {
            parent::saveorder();
        } else {
            // Nothing to reorder
            $this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
            return true;
        }
    }

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     *
     * @since   3.0
     */
    public function saveOrderAjax() {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        // Get the input
        $input = JFactory::getApplication()->input;
        $pks = $input->post->get('cid', array(), 'array');
        $order = $input->post->get('order', array(), 'array');
        $originalOrder = explode(',', $this->input->getString('original_order_values'));

        // Make sure something has changed
        if (!($order === $originalOrder)) {
            // Get the model
            $model = $this->getModel();
            // Save the ordering
            $return = $model->saveorder($pks, $order);
            if ($return) {
                echo "1";
            }
        }

        // Close the application
        JFactory::getApplication()->close();
    }

}