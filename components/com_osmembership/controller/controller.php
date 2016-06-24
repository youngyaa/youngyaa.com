<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     OSMembership
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class OSMembershipController extends MPFController
{

	/**
	 * Method to display a view
	 *
	 * This function is provide as a default implementation, in most cases
	 * you will need to override it in your own controllers.
	 *
	 * @param boolean $cachable  If true, the view output will be cached
	 *
	 * @param array   $urlparams An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return MPFController A MPFController object to support chaining.
	 */

	public function display($cachable = false, array $urlparams = array())
	{
		$document = JFactory::getDocument();
		$config   = OSMembershipHelper::getConfig();
		$baseUrl  = JUri::base(true);

		$document->addStylesheet($baseUrl . '/components/com_osmembership/assets/css/style.css', 'text/css', null, null);
		$document->addStylesheet($baseUrl . '/components/com_osmembership/assets/css/custom.css', 'text/css', null, null);

		if (@$config->load_jquery !== '0')
		{
			OSMembershipHelper::loadJQuery();
		}

		OSMembershipHelper::loadBootstrap(true);

		JHtml::_('script', OSMembershipHelper::getSiteUrl() . 'components/com_osmembership/assets/js/jquery-noconflict.js', false, false);

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Process downloading invoice for a subscription record based on given ID
	 */
	public function download_invoice()
	{		
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_osmembership/table');
		$id = $this->input->getInt('id', 0);
		$row    = JTable::getInstance('osmembership', 'Subscriber');
		$row->load($id);

		// Check download invoice permission
		$canDownload = false;
		if ($row)
		{
			$user = JFactory::getUser();
			if ($user->authorise('core.admin') || ($row->user_id > 0 && ($row->user_id == $user->id)))
			{
				$canDownload = true;
			}
		}

		if ($canDownload)
		{
			OSMembershipHelper::downloadInvoice($id);
		}
		else
		{
			JError::raiseError(403, JText::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'));
		}
	}

	/**
	 * Download a file uploaded by users
	 *
	 * @throws Exception
	 */
	public function download_file()
	{
		// Todo: Should we make some basic validation here?
		$filePath = JPATH_ROOT . '/media/com_osmembership/upload/';
		$fileName = $this->input->get('file_name', '', 'string');
		$fileName = basename($fileName);
		if (file_exists($filePath . $fileName))
		{
			while (@ob_end_clean()) ;
			OSMembershipHelper::processDownload($filePath . $fileName, $fileName, true);
			exit();
		}
		else
		{
			JFactory::getApplication()->redirect('index.php?option=com_osmembership&Itemid=' . $this->input->getInt('Itemid'), JText::_('OSM_FILE_NOT_EXIST'));
		}
	}
}