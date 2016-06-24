<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

/**
 * OSemmbership Component Plan Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelPlan extends MPFModelAdmin
{

	/**
	 * This model process events, so we need to set triggerEvents to true
	 *
	 * @var bool
	 */
	protected $triggerEvents = true;

	/**
	 * Constructor.
	 *
	 * @param array $config An optional associative array of configuration settings.
	 */
	public function __construct($config = array())
	{
		$config['event_after_save'] = 'onAfterSaveSubscriptionPlan';

		parent::__construct($config);
	}

	/**
	 * Initialize the plan data for adding new record
	 */
	protected function initData()
	{
		parent::initData();
		$this->data->enable_renewal = 1;
	}

	/**
	 * Pre-process data, delete old thumbnail if required, upload new thumbnail
	 *
	 * @param      $row
	 * @param MPFInput  $input
	 * @param bool $isNew
	 */
	protected function beforeStore($row, $input, $isNew)
	{
		// Delete the old thumbnail if required
		$thumbPath = JPATH_ROOT . '/media/com_osmembership/';
		if (!$isNew && $input->has('del_thumb') && $row->thumb)
		{
			if (JFile::exists($thumbPath . $row->thumb))
			{
				JFile::delete($thumbPath . $row->thumb);
			}
			$input->set('thumb', '');
		}

		// Process uploading thumb image
		$thumbImage = $input->files->get('thumb_image');
		if ($thumbImage['name'])
		{
			$fileExt        = JString::strtoupper(JFile::getExt($thumbImage['name']));
			$supportedTypes = array('JPG', 'PNG', 'GIF');
			if (in_array($fileExt, $supportedTypes))
			{
				if (JFile::exists($thumbPath . JString::strtolower($thumbImage['name'])))
				{
					$fileName = time() . '_' . JString::strtolower($thumbImage['name']);
				}
				else
				{
					$fileName = JString::strtolower($thumbImage['name']);
				}
				$fileName = JFile::makeSafe($fileName);
				JFile::upload($_FILES['thumb_image']['tmp_name'], $thumbPath . $fileName);
				$input->set('thumb', $fileName);
			}
		}

		$paymentMethods = $input->get('payment_methods', array(), 'array');
		if (empty($paymentMethods[0]))
		{
			$input->set('payment_methods', '');
		}
		else
		{
			$input->set('payment_methods', implode(',', $paymentMethods));
		}
	}

	/**
	 * Store extra data like renew options, upgrade options
	 *
	 * @param JTable   $row
	 * @param MPFInput $input
	 * @param bool     $isNew
	 */
	protected function afterStore($row, $input, $isNew)
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);
		$planId = (int) $row->id;

		// Delete the existing data of this plan
		if (!$isNew)
		{
			$query->delete('#__osmembership_renewrates')
				->where('plan_id =' . $planId);
			$db->setQuery($query);
			$db->execute();

			$query->clear();
			$query->delete('#__osmembership_upgraderules')
				->where('from_plan_id = ' . $planId);
			$db->setQuery($query);
			$db->execute();
		}

		$data = $input->getData();

		// Store renew options
		if (isset($data['number_days']))
		{
			$query->clear();
			$execute = false;
			for ($i = 0, $n = count($data['number_days']); $i < $n; $i++)
			{
				$numberDays = (int) $data['number_days'][$i];
				$price = $data['renew_price'][$i];
				if ($numberDays > 0 && $price > 0)
				{
					$execute = true;
					$query->values("$planId, $numberDays, $price");
				}
			}
			if ($execute)
			{
				$query->insert('#__osmembership_renewrates')
					->columns('plan_id, number_days, price');
				$db->setQuery($query);
				$db->execute();
			}
		}

		// Store upgrade options
		if (isset($data['to_plan_id']))
		{
			$query->clear();
			$execute = false;
			for ($i = 0; $i < count($data['to_plan_id']); $i++)
			{
				$toPlan = $data['to_plan_id'][$i];
				$price = floatval($data['upgrade_price'][$i]);
				$publishedRule = $data['rule_published'][$i];
				if ($toPlan > 0)
				{
					$query->values("$planId, $toPlan, $price, $publishedRule");
					$execute = true;
				}
			}
			if ($execute)
			{
				$query->insert('#__osmembership_upgraderules')
					->columns('from_plan_id, to_plan_id, price, published');

				$db->setQuery($query);
				$db->execute();
			}
		}
	}

	/**
	 * Delete the related records before deleting the actual record
	 *
	 * @param array $cid
	 */
	protected function beforeDelete($cid)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__osmembership_articles')
			->where('plan_id IN ('.implode(',', $cid).')');
		$db->setQuery($query);
		$db->execute();

		//Delete from URL tables as well
		if (JPluginHelper::isEnabled('osmembership', 'urls'))
		{
			$query->clear();
			$query->delete('#__osmembership_urls')
				->where('plan_id IN ('.implode(',', $cid).')');
			$db->setQuery($query);
			$db->execute();
		}
	}
}