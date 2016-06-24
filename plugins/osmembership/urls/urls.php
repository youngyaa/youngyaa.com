<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

class plgOSMembershipUrls extends JPlugin
{	
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);			
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_osmembership/tables');
	}
	/**
	 * Render settings from
	 * @param PlanOSMembership $row
	 */
	function onEditSubscriptionPlan($row) {	
		ob_start();
		$this->_drawSettingForm($row);		
		$form = ob_get_contents();	
		ob_end_clean();		
		return array('title' => JText::_('PLG_OSMEMBERSHIP_JOOMLA_URLS_SETTINGS'),							
					'form' => $form
		) ;				
	}

	/**
	 * Store setting into database
	 * @param PlanOsMembership $row
	 * @param Boolean $isNew true if create new plan, false if edit
	 */
	function onAfterSaveSubscriptionPlan($context, $row, $data,$isNew) {
		$db = JFactory::getDbo() ;		
		$urls = explode("\r\n", $data['urls']) ;
		if (!$isNew) {
			$sql = 'DELETE FROM #__osmembership_urls WHERE plan_id='.$row->id ;
			$db->setQuery($sql) ;
			$db->query();											
		}
		if (count($urls)) {
			foreach ($urls as $url) {
				$url = trim($url);
				$url = $db->quote($url);
				$sql = "INSERT INTO #__osmembership_urls(plan_id, url) VALUES($row->id, $url)" ;
				$db->setQuery($sql) ;
				$db->query();						
			}	
		}		
	}	
	/**
	 * Display form allows users to change setting for this subscription plan 
	 * @param object $row
	 * 
	 */	
	function _drawSettingForm($row) {		
		if ($row->id > 0) {
			$db = JFactory::getDbo() ;
			$sql = 'SELECT url FROM #__osmembership_urls WHERE plan_id='.$row->id ;
			$db->setQuery($sql) ;
			$urls = $db->loadColumn() ;
			$urls = implode("\r\n", $urls);
		}
				
	?>	
		<table class="admintable adminform" style="width: 90%;">
				<tr>
					<td class="key" width="110">
						<?php echo  JText::_('PLG_OSMEMBERSHIP_JOOMLA_URLS'); ?>
					</td>
					<td>
						<textarea rows="20" cols="70" name="urls" class="input-xxlarge"><?php echo $urls ; ?></textarea>
					</td>
					<td>
						<?php echo JText::_('PLG_OSMEMBERSHIP_JOOMLA_URLS_EXPLAIN'); ?>
					</td>
				</tr>				
		</table>	
	<?php							
	}
}	