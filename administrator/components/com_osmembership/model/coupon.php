<?php
/**
 * @version        2.2.1
 * @package        Joomla
 * @subpackage     Membership Pro
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2012 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die ();

/**
 * OS Membership Component Coupon Model
 *
 * @package        Joomla
 * @subpackage     Membership Pro
 */
class OSMembershipModelCoupon extends MPFModelAdmin
{
	/**
	 * @param $input
	 *
	 * @return int
	 * @throws Exception
	 */
	public function import($input)
	{
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
		$coupons = static::getCouponCSV($input);

		// Get list of plans
		$query->clear();
		$query->select('id, title')
			->from('#__osmembership_plans');
		$db->setQuery($query);
		$rows  = $db->loadObjectList();
		$plans = array();
		foreach ($rows as $row)
		{
			$plans[JString::strtolower($row->title)] = $row->id;
		}

		if (count($coupons))
		{
			$imported = 0;
			foreach ($coupons as $coupon)
			{
				$row = $this->getTable();
				//get plan Id
				$planTitle         = JString::strtolower($coupon['plan']);
				$planId            = isset($plans[$planTitle]) ? $plans[$planTitle] : 0;
				$coupon['plan_id'] = $planId;
				if ($coupon['valid_from'])
				{
					$coupon ['valid_from'] = JHtml::date($coupon['valid_from'], 'Y-m-d');
				}
				else
				{
					$coupon ['valid_from'] = '';
				}

				if ($coupon['valid_to'])
				{
					$coupon ['valid_to'] = JHtml::date($coupon['valid_to'], 'Y-m-d');
				}
				else
				{
					$coupon ['valid_to'] = '';
				}
				$row->bind($coupon);
				$row->store();
				$imported++;
			}
		}

		return $imported;
	}

	/**
	 * Generate batch coupon
	 *
	 * @param RADInput $input
	 */
	public function batch($input)
	{
		$numberCoupon        = $input->getInt('number_coupon', 50);
		$charactersSet       = $input->getString('characters_set');
		$prefix              = $input->getString('prefix');
		$length              = $input->getInt('length', 20);
		$data                = array();
		$data['discount']    = $input->getFloat('discount', 0);
		$data['coupon_type'] = $input->getInt('coupon_type', 0);
		$data['times']       = $input->getInt('times');

		$data['plan_id'] = $input->getInt('plan_id', 0);

		if ($input->getString('valid_from'))
		{
			$data ['valid_from'] = JHtml::date($input->getString('valid_from'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_from'] = '';
		}

		if ($input->getString('valid_to'))
		{
			$data ['valid_to'] = JHtml::date($input->getString('valid_to'), 'Y-m-d', null);
		}
		else
		{
			$data ['valid_to'] = '';
		}
		$data['used']       = 0;
		$data ['published'] = $input->getInt('published');

		for ($i = 0; $i < $numberCoupon; $i++)
		{
			$salt         = static::genRandomCoupon($length, $charactersSet);
			$couponCode   = $prefix . $salt;
			$row          = $this->getTable();
			$data['code'] = $couponCode;

			$row->bind($data);
			$row->store();
		}
	}

	/**
	 * Generate random Coupon
	 *
	 * @param int    $length
	 * @param string $charactersSet
	 *
	 * @return string
	 */
	private static function genRandomCoupon($length = 8, $charactersSet)
	{
		$salt = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

		if ($charactersSet)
		{
			$salt = $charactersSet;
		}

		$base     = strlen($salt);
		$makePass = '';

		/*
		 * Start with a cryptographic strength random string, then convert it to
		 * a string with the numeric base of the salt.
		 * Shift the base conversion on each character so the character
		 * distribution is even, and randomize the start shift so it's not
		 * predictable.
		 */
		$random = JCrypt::genRandomBytes($length + 1);
		$shift  = ord($random[0]);

		for ($i = 1; $i <= $length; ++$i)
		{
			$makePass .= $salt[($shift + ord($random[$i])) % $base];
			$shift += ord($random[$i]);
		}

		return $makePass;
	}

	/**
	 * Get subscribers data from csv file
	 *
	 * @param $input
	 *
	 * @return array
	 */
	private static function getCouponCSV($input)
	{
		$keys    = array();
		$coupons = array();
		$coupon  = array();
		$allowedExts = array('csv');
		$csvFile     = $input->files->get('csv_coupons');
		$csvFileName = $csvFile ['tmp_name'];
		$fileName    = $csvFile ['name'];
		$fileExt     = strtolower(JFile::getExt($fileName));
		if (in_array($fileExt, $allowedExts))
		{
			$line = 0;
			$fp   = fopen($csvFileName, 'r');
			while (($cells = fgetcsv($fp)) !== false)
			{
				if ($line == 0)
				{
					foreach ($cells as $key)
					{
						$keys [] = $key;
					}
					$line++;
				}
				else
				{
					$i = 0;
					foreach ($cells as $cell)
					{
						$coupon [$keys [$i]] = $cell;
						$i++;
					}
					$coupons [] = $coupon;
				}
			}
			fclose($fp);

			return $coupons;
		}
	}
}