<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Stn_events', JPATH_COMPONENT);

// Execute the task.
$controller = JControllerLegacy::getInstance('Stn_events');
$controller->execute(JFactory::getApplication()->input->get('task'));
$controller->redirect();
