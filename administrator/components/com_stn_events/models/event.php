<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Stn_events
 * @author     Pawan <goyalpawan89@gmail.com>
 * @copyright  2016 Pawan
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Stn_events model.
 *
 * @since  1.6
 */
class Stn_eventsModelEvent extends JModelAdmin
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    1.6
	 */
	protected $text_prefix = 'COM_STN_EVENTS';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.2
	 */
	public $typeAlias = 'com_stn_events.event';

	/**
	 * @var null  Item data
	 * @since  1.6
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    1.6
	 */
	public function getTable($type = 'Event', $prefix = 'Stn_eventsTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_stn_events.event', 'event',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

			if($form->getFieldAttribute('starttime', 'default') == 'NOW'){
				$form->setFieldAttribute('starttime', 'default', date('H:i:s'));
			}
			if($form->getFieldAttribute('endtime', 'default') == 'NOW'){
				$form->setFieldAttribute('endtime', 'default', date('H:i:s'));
			}
		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_stn_events.edit.event.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Method to duplicate an Event
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_stn_events'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}
				
				if (!empty($table->eventimage))
				{
					if (is_array($table->eventimage))
					{
						$table->eventimage = implode(',', $table->eventimage);
					}
				}
				else
				{
					$table->eventimage = '';
				}

				if (!empty($table->prizeimage))
				{
					if (is_array($table->prizeimage))
					{
						$table->prizeimage = implode(',', $table->prizeimage);
					}
				}
				else
				{
					$table->prizeimage = '';
				}


				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__stn_events');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
	public function saveeventSetting($data){
		//print_r($data); die;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$error = '';
		$title = trim($data['title']);
		$description = trim($data['description']);
		$rules = trim($data['rules']);
		$startdate = trim($data['startdate']);
		$enddate = trim($data['enddate']);
		if($data['title'] == ''){
			$error .= '<p>Title Required</p>';
		}
		if($data['description'] == ''){
			$error .= '<p>Description Required</p>';
		}
		if($data['rules'] == ''){
			$error .= '<p>Rules Required</p>';
		}
		if($data['startdate'] == ''){
			$error .= '<p>Start Date Required</p>';
		}
		if($data['enddate'] == ''){
			$error .= '<p>End Date Required</p>';
		}
		if($data['startdate'] >= $data['enddate']){
			$error .= '<p>End Date Should Greter To Start Date</p>';
		}
		if($error == ''){
			$datacheck = "SELECT count(id) FROM #__stn_event_setting WHERE id = 1";
			$db->setQuery($datacheck);
			$dataCheck = $db->loadResult();
			//echo $dataCheck; die;
			if($dataCheck == 1){
				$datacheck = "UPDATE #__stn_event_setting SET title = '".addslashes($title)."', description = '".addslashes($description)."', rules = '".addslashes($rules)."', startdate = '".$startdate."', enddate = '".$enddate."' WHERE id = 1";
			} else {
				$datatrun = "TRUNCATE TABLE  #__stn_event_setting";
				$db->setQuery($datatrun);
				$db->execute();
				$datacheck = "INSERT INTO #__stn_event_setting (title, description, rules, startdate, enddate) VALUES ('".addslashes($title)."','".addslashes($description)."','".addslashes($rules)."','".$startdate."','".$enddate."')";
			}
			$dates = $this->createDateRange($startdate,$enddate);
			$db->setQuery($datacheck);
			$db->execute();
			//echo '<pre>';
			//print_r($dates);
			//die;
			foreach($dates as $dat){
				$sqlCheckdat = 'SELECT count(id) FROM #__stn_events_dates WHERE title = "'.$dat.'"';
				$db->setQuery($sqlCheckdat);
				$datey = $db->loadResult();
				if($datey == 0){
					$sqlinsertDate = 'INSERT INTO #__stn_events_dates (title,status) VALUES ("'.$dat.'","1")';
					//echo $sqlinsertDate; die;
					$db->setQuery($sqlinsertDate);
					$db->execute();
				}
			}
			$deletesql = "DELETE FROM #__stn_events_dates WHERE `title` < '".$startdate."' OR `title` > '".$enddate."'";
			//echo $deletesql; die;
			$db->setQuery($deletesql);
			$db->execute();
			$success = 1;
			return $success;
		} else {
			return $error;
		}
	}
	
	public function createDateRange($startDate, $endDate, $format = "Y-m-d")
	{
		$date_from = $startDate;   
		$date_from = strtotime($date_from); // Convert date to a UNIX timestamp  
		  
		// Specify the end date. This date can be any English textual format  
		$date_to = $endDate;  
		$date_to = strtotime($date_to); // Convert date to a UNIX timestamp  
		  
		// Loop from the start date to end date and output all dates inbetween  
		for ($i=$date_from; $i<=$date_to; $i+=86400) {  
			$range[] = date("Y-m-d", $i);  
		}  
		return $range;
	}
	
	public function getDateDetail(){		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$id = $_GET['id'];
		$datacheck = "SELECT title FROM #__stn_events_dates WHERE id = ".$id;
		$db->setQuery($datacheck);
		$dataCheck = $db->loadResult();
		return $dataCheck;
	}
	
	public function getTimeslots(){		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$id = $_GET['id'];
		$datacheck = "SELECT * FROM #__stn_events_timeslotes WHERE date_id = ".$id;
		$db->setQuery($datacheck);
		$dataCheck = $db->loadObjectList();
		return $dataCheck;
	}
	
	
	public function getTimeslotDate(){		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$id = $_GET['id'];
		$datacheck = "SELECT ts.*, dt.title FROM #__stn_events_timeslotes as ts INNER JOIN #__stn_events_dates as dt ON ts.date_id = dt.id WHERE ts.id = ".$id;
		$db->setQuery($datacheck);
		$dataCheck = $db->loadObject();
		return $dataCheck;
	}
	
	
	public function getTimeslotgrabers(){		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$id = $_GET['id'];
		$datacheck = "SELECT gb.id as gbid, gb.created as gbcreated, u.* FROM #__stn_events_grabers as gb INNER JOIN #__users as u ON gb.user_id = u.id WHERE gb.timesloat_id = ".$id." ORDER BY gb.id";
		$db->setQuery($datacheck);
		$dataCheck = $db->loadObjectList();
		return $dataCheck;
	}
	
	public function saveeventTimeSloates($data){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$jinput = JFactory::getApplication()->input;
		$files = $jinput->files->get('jform');
		$ids = $data['id'];
		$starttime = $data['starttime'];
		$endtime = $data['endtime'];
		$prize = $data['prize'];
		$prizeprovider = $data['prizeprovider'];
		$prizedescription = $data['prizedescription'];
		$prizeimage1 = $this->uploadimage($files['prizeimage']);
		$prizeimage = $data['prizeimage'];
		$date_id = $data['date_id'];
		$error = '';
		/*echo '<pre>'; print_r($data);
		print_r($files);
		print_r($prizeimage1);
		print_r($prizeimage); die;*/
		foreach($starttime as $k => $st){
			if($st >= $endtime[$k]){
				$error .= '<p>Select Start Time less then end time.</p>';
			}
			foreach($starttime as $s => $stt){
				if($s != $k){
					if($st >= $stt && $st <= $endtime[$s]){
						$error .= '<p>Check Time Sloat Intervel.</p>';
					}
				}
			}
		}
		if($error == ''){
			$allowedids = array();
			foreach($ids as $ik => $id){
				if($id == ''){
					$sql = "INSERT INTO #__stn_events_timeslotes (date_id,starttime,endtime,prize,prizeprovider,prizedescription,prizeimage) VALUES ('".$date_id."','".$starttime[$ik]."','".$endtime[$ik]."','".addslashes($prize[$ik])."','".addslashes($prizeprovider[$ik])."','".addslashes($prizedescription[$ik])."','".addslashes(str_replace('images/stnevents/','',$prizeimage1[$ik]))."')";
					$db->setQuery($sql);
					$db->execute();
					$allowedids[] = $db->insertid();
				} else {
					$sql = "UPDATE #__stn_events_timeslotes SET starttime = '".$starttime[$ik]."', endtime = '".$endtime[$ik]."', prize = '".addslashes($prize[$ik])."', prizeprovider = '".addslashes($prizeprovider[$ik])."', prizedescription = '".addslashes($prizedescription[$ik])."'";
					if($prizeimage[$id] != ''){
						$sql .= ", prizeimage = '".addslashes(str_replace('images/stnevents/','',$prizeimage[$id]))."'";
					}
					$sql .= " WHERE id = ".$id;						
					$db->setQuery($sql);
					$db->execute();				
					$allowedids[] = $id;
				}
			}
			if(count($allowedids) > 0){
				$sqldelete = "DELETE FROM #__stn_events_timeslotes WHERE id NOT IN (".implode(',',$allowedids).") AND date_id = ".$date_id;
				$db->setQuery($sqldelete);
				$db->execute();
			}
			$success = 1;
			return $success;
		} else {
			return $error;
		}
		die;
	}
	
	public function uploadimage($files){
		//echo '<pre>'; print_r($files); die;
		$images = array();
		foreach ($files as $singleFile)
		{
			//echo '<pre>'; print_r($singleFile);
			jimport('joomla.filesystem.file');
			$fileError = $singleFile['error'];
			$message = '';
			if ($fileError > 0 && $fileError != 4)
			{
				switch ($fileError)
				{
					case 1:
						$message = JText::_('File size exceeds allowed by the server');
						break;
					case 2:
						$message = JText::_('File size exceeds allowed by the html form');
						break;
					case 3:
						$message = JText::_('Partial upload error');
						break;
				}
				if ($message != '')
				{
					$app->enqueueMessage($message, 'warning');
					return false;
				}
			}
			elseif ($fileError == 4)
			{
				$images[] = '';
			}
			else
			{
				jimport('joomla.filesystem.file');
				$filename = JFile::stripExt($singleFile['name']);
				$extension = JFile::getExt($singleFile['name']);
				$filename = preg_replace("/[^A-Za-z0-9]/i", "-", $filename);
				$filename = $filename . '.' . $extension;
				$uploadPath = JPATH_ROOT . '/images/stnevents/' . $filename;
				$fileTemp = $singleFile['tmp_name'];
				if (!JFile::exists($uploadPath))
				{
					if (!JFile::upload($fileTemp, $uploadPath))
					{
						$app->enqueueMessage('Error moving file', 'warning');
						return false;
					}
				}
				$images[] = 'images/stnevents/'.$filename;
			}
		}
		return $images;	
	}
}
