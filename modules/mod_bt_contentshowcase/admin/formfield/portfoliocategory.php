<?php
// No direct access to this file
defined('_JEXEC') or die;
 
// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');
 
/**
 * PortfolioCategory Form Field class for the bt_portfolio component
 */
class JFormFieldPortfolioCategory extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var		string
	 */
	protected $type = 'PortfolioCategory';
 
	protected function getInput(){
		$db = JFactory::getDBO();
		$db->setQuery("SELECT enabled FROM #__extensions WHERE name = 'bt_portfolio'");
		$isEnabled = $db->loadResult();	
		if($isEnabled){	
			return parent::getInput();
		}else{
			return '<span class="' . $this->element['class'] . '">' . JText::_('BT_PORTFORLIO_IS_NOT_ENABLED_OR_INSTALLED') . '</span>';
		}
	}
	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 */
	protected function getOptions() 
	{	
		JLoader::register('Bt_portfolioHelper', JPATH_ADMINISTRATOR.'/components/com_bt_portfolio/helpers/helper.php');
		$options = Bt_portfolioHelper::getCategoryOptions();
		$options = array_merge(parent::getOptions(), $options);
		return $options;
		
	}
	
}
