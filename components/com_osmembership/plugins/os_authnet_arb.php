<?php
/**
 * @version		2.9.1
 * @package        Joomla
 * @subpackage	Joom Donation
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

class os_authnet_arb
{

	/**
	 * Auth merchant ID
	 *
	 * @var string
	 */
	var $login = null;

	/**
     * Auth transaction key 
     *
     * @var string
     */
	var $transkey = null;

	/**
     * Test or live mode
     *
     * @var boolean
     */
	var $mode = true;

	/**
	 * Params which will be passed to authorize.net
	 *
	 * @var string
	 */
	var $params = array();

	/**
     * Success or not
     *
     * @var boolean
     */
	var $success = false;

	/**
     * Error or not
     *
     * @var boolean
     */
	var $error = true;

	var $xml;

	var $response;

	var $resultCode;

	var $code;

	var $text;

	var $subscrId;

	var $data = array();

	/**
	 * Constructor function
	 *
	 * @param object $config
	 */
	function os_authnet_arb($params)
	{
		$this->mode = $params->get('authnet_mode');
		$this->login = $params->get('x_login');
		$this->transkey = $params->get('x_tran_key');
		if ($this->mode)
		{
			$this->url = "https://api.authorize.net/xml/v1/request.api";
		}
		else
		{
			$this->url = "https://apitest.authorize.net/xml/v1/request.api";
		}
		$this->params['startDate'] = date("Y-m-d");
		$this->params['trialOccurrences'] = 0;
		$this->params['trialAmount'] = 0.00;

		// Log Authorize.net data
		$this->ipn_log = true;
		$this->ipn_log_file = JPATH_COMPONENT . '/authnet_ipn_logs.txt';
	}

	/**
	 * Process payment
	 *
	 * @param int $retries Number of retries if error appear
	 */
	function process($retries = 1)
	{
		$count = 0;
		while ($count < $retries)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
			curl_setopt($ch, CURLOPT_HEADER, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $this->xml);
			
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			$this->response = curl_exec($ch);
			$this->parseResults();
			if ($this->resultCode === "Ok")
			{
				$this->success = true;
				$this->error = false;
				break;
			}
			else
			{
				$this->success = false;
				$this->error = true;
				break;
			}
			$count++;
		}
		curl_close($ch);
	}

	/**
	 * Perform a recurring payment subscription
	 *
	 */
	function createAccount()
	{
		$this->xml = "<?xml version='1.0' encoding='utf-8'?>
          <ARBCreateSubscriptionRequest xmlns='AnetApi/xml/v1/schema/AnetApiSchema.xsd'>
              <merchantAuthentication>
                  <name>" . $this->login . "</name>
                  <transactionKey>" . $this->transkey . "</transactionKey>
              </merchantAuthentication>
              <refId>" . $this->params['refID'] . "</refId>
              <subscription>
                  <name>" . $this->params['subscrName'] . "</name>
                  <paymentSchedule>
                      <interval>
                          <length>" . $this->params['interval_length'] . "</length>
                          <unit>" . $this->params['interval_unit'] . "</unit>
                      </interval>
                      <startDate>" . $this->params['startDate'] . "</startDate>
                      <totalOccurrences>" . $this->params['totalOccurrences'] . "</totalOccurrences>
                      <trialOccurrences>" . $this->params['trialOccurrences'] . "</trialOccurrences>
                  </paymentSchedule>
                  <amount>" . $this->params['amount'] . "</amount>
                  <trialAmount>" . $this->params['trialAmount'] . "</trialAmount>
                  <payment>
                      <creditCard>
                          <cardNumber>" . $this->params['cardNumber'] . "</cardNumber>
                          <expirationDate>" . $this->params['expirationDate'] . "</expirationDate>
                      </creditCard>
                  </payment>
                  <billTo>
                      <firstName>" . $this->params['firstName'] . "</firstName>
                      <lastName>" . $this->params['lastName'] . "</lastName>
                      <address>" . $this->params['address'] . "</address>
                      <city>" . $this->params['city'] . "</city>
                      <state>" . $this->params['state'] . "</state>
                      <zip>" . $this->params['zip'] . "</zip>
                  </billTo>
              </subscription>
          </ARBCreateSubscriptionRequest>";					
		$this->process();
	}

	/**
	 * Set paramter
	 *
	 * @param string $field
	 * @param string $value
	 */
	function setParameter($field = "", $value = null)
	{
		$field = (is_string($field)) ? trim($field) : $field;
		$value = (is_string($value)) ? trim($value) : $value;
		$this->params[$field] = $value;
	}

	/**
	 * Parse the xml to get the necessary information
	 *
	 */
	function parseResults()
	{
		$this->resultCode = self::substring_between($this->response, '<resultCode>', '</resultCode>');
		$this->code = self::substring_between($this->response, '<code>', '</code>');
		$this->text = self::substring_between($this->response, '<text>', '</text>');
		$this->subscrId = self::substring_between($this->response, '<subscriptionId>', '</subscriptionId>');				
	}
	
	public static function substring_between($haystack,$start,$end)
	{
		if (strpos($haystack,$start) === false || strpos($haystack,$end) === false)
		{
			return false;
		}
		else
		{
			$start_position = strpos($haystack,$start)+strlen($start);
			$end_position = strpos($haystack,$end);
			return substr($haystack,$start_position,$end_position-$start_position);
		}
	}
	
	function getSubscriberID()
	{
		return $this->subscrId;
	}

	function isSuccessful()
	{
		return $this->success;
	}

	function isError()
	{
		return $this->error;
	}

	/**
     * Processs payment 
     *
     * @param string $data
     * @return unknown
     */
	function processRecurringPayment($row, $data)
	{		
		$app = JFactory::getApplication();
		$db = JFactory::getDbo();
		$Itemid = JRequest::getInt('Itemid');
		$sql = 'SELECT * FROM #__osmembership_plans WHERE id=' . $row->plan_id;
		$db->setQuery($sql);
		$rowPlan = $db->loadObject();
		$frequency = $rowPlan->subscription_length_unit;
		$length = $rowPlan->subscription_length;		
		switch ($frequency)
		{
			case 'D':
				$unit = 'days';
				break;
			case 'W':
				$unit = 'days';
				break;
			case 'M':
				$unit = 'months';
				break;
			case 'Y':
				$length = 12;
				$unit = 'months';
				break;
		}
		$this->setParameter('refID', $row->subscription_code);
		$this->setParameter('subscrName', $row->first_name . ' ' . $row->last_name);
		$this->setParameter('interval_length', $length);
		$this->setParameter('interval_unit', $unit);
		$this->setParameter('expirationDate', str_pad($data['exp_month'], 2, '0', STR_PAD_LEFT) . '/' . substr($data['exp_year'], 2, 2));
		$this->setParameter('cardNumber', $data['x_card_num']);
		$this->setParameter('firstName', $row->first_name);
		$this->setParameter('lastName', $row->last_name);
		$this->setParameter('address', $row->address);
		$this->setParameter('city', $row->city);
		$this->setParameter('state', $row->state);
		$this->setParameter('zip', $row->zip);
		$this->setParameter('amount', round($data['regular_price'], 2));
		if ($rowPlan->number_payments >= 2)
		{
			$totalOccurences = $rowPlan->number_payments;
		}
		else
		{
			$totalOccurences = 9999;
		}
		$this->setParameter('totalOccurrences', $totalOccurences);
		if ($rowPlan->trial_duration) {
			$this->setParameter('trialAmount', round($data['trial_amount'], 2));
			$this->setParameter('trialOccurrences', $rowPlan->trial_duration) ;	
		}					
		$this->createAccount();
		if ($this->success)
		{
			$config               = OSMembershipHelper::getConfig();
			$row->subscription_id = $this->getSubscriberID();
			$row->payment_date   = date('Y-m-d H:i:s');
			$row->payment_made   = 1;
			$row->published      = true;
			$row->store();
			JPluginHelper::importPlugin('osmembership');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onMembershipActive', array($row));
			OSMembershipHelper::sendEmails($row, $config);
			$app->redirect(JRoute::_('index.php?option=com_osmembership&view=complete&Itemid=' . $Itemid, false, false));

			return true;
		}
		else
		{
			$_SESSION['reason'] = $this->text;
			$app->redirect(JRoute::_('index.php?option=com_osmembership&view=failure&id=' . $row->id . '&Itemid=' . $Itemid, false));

			return false;
		}
	}


	function _validate()
	{
		$this->data = $_POST;
		$this->log_ipn_results();
		if (!empty($this->data['x_subscription_id']) && @$this->data['x_response_code'] == 1 && ($this->data['x_subscription_paynum'] > 1))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Log IPN result
	 *
	 * @param string $success
	 */
	function log_ipn_results()
	{
		if (!$this->ipn_log)
		{
			return;
		}
		$text = '[' . date('m/d/Y g:i A') . '] - ';
		$text .= "IPN POST Vars from Authorize.net:\n";
		foreach ($this->_data as $key => $value)
		{
			$text .= "$key=$value, ";
		}
		$fp = fopen($this->ipn_log_file, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp); // close file
	}

	function verifyRecurringPayment()
	{
		$ret = $this->_validate();
		if ($ret)
		{
			// Find the ID of the subscription record
			$subscriptionId = $this->data['x_subscription_id'];
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__osmembership_subscribers')
				->where('subscription_id = '. $db->quote($subscriptionId));
			$db->setQuery($query);
			$id = (int) $db->loadResult();
			if ($id)
			{
				OSMembershipHelper::extendRecurringSubscription($id, $this->data['x_trans_id']);
			}
		}
	}

	public function cancelSubscription($row)
	{

		$this->xml =
			"<?xml version=\"1.0\" encoding=\"utf-8\"?>".
			"<ARBCancelSubscriptionRequest xmlns=\"AnetApi/xml/v1/schema/AnetApiSchema.xsd\">".
			"<merchantAuthentication>".
			"<name>" . $this->login . "</name>".
			"<transactionKey>" . $this->transkey . "</transactionKey>".
			"</merchantAuthentication>" .
			"<subscriptionId>" . $row->subscription_id. "</subscriptionId>".
			"</ARBCancelSubscriptionRequest>";
		$this->process();
		if ($this->success)
		{
			return true;
		}
		else
		{
			JFactory::getApplication()->enqueueMessage($this->text, 'error');
			return false;
		}
	}
}
?>