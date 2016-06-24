<?php
/**
 * @version        	2.0.0
 * @package        	Joomla
 * @subpackage		Event Booking
 * @author  		Tuan Pham Ngoc
 * @copyright    	Copyright (C) 2010 - 2015 Ossolution Team
 * @license        	GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class os_worldpay extends os_payment
{

	/**
	 * Worldpay mode 
	 *
	 * @var boolean live mode : true, test mode : false
	 */
	var $_mode = 0;

	/**
	 * Paypal url
	 *
	 * @var string
	 */
	var $_url = null;

	/**
	 * Array of params will be posted to server
	 *
	 * @var string
	 */
	var $_params = array();

	/**
	 * Array containing data posted from paypal to our server
	 *
	 * @var array
	 */
	var $_data = array();

	/**
	 * Constructor functions, init some parameter
	 *
	 * @param object $config
	 */
	function os_worldpay($params)
	{
		parent::setName('os_worldpay');
		parent::os_payment();
		parent::setCreditCard(false);
		parent::setCardType(false);
		parent::setCardCvv(false);
		parent::setCardHolderName(false);
		$this->ipn_log = true;
		$this->ipn_log_file = JPATH_COMPONENT . '/ipn_logs.txt';
		$this->setParam('instId', $params->get('wp_installation_id'));
		$this->setParam('currency', 'GBP');
		if (!$params->get('worldpay_mode'))
		{
			$this->setParam('testMode', '100');
			$this->_url = 'https://secure-test.worldpay.com/wcc/purchase';
		}
		else
		{
			$this->_url = 'https://secure.worldpay.com/wcc/purchase';
		}
	}

	/**
	 * Set param value
	 *
	 * @param string $name
	 * @param string $val
	 */
	function setParam($name, $val)
	{
		$this->_params[$name] = $val;
	}

	/**
	 * Setup payment parameter
	 *
	 * @param array $params
	 */
	function setParams($params)
	{
		foreach ($params as $key => $value)
		{
			$this->_params[$key] = $value;
		}
	}

	/**
	 * Process payment
	 *
	 * @param object $row The registration record
	 * @param array $data
	 */
	function processPayment($row, $data)
	{		
		$itemName = JText::_('EB_EVENT_REGISTRATION');
		$itemName = str_replace('[EVENT_TITLE]', $data['event_title'], $itemName);
		$this->setParam('desc', $itemName);
		$this->setParam('amount', round($data['amount'], 2));
		$this->setParam('cartId', $row->id);
		$this->setParam('address', $row->address . '&#10' . $row->address2 . '&#10' . $row->city . '&#10' . $row->state);
		$this->setParam('name', $row->first_name . ' ' . $row->last_name);
		//Get country code here
		$countryCode = EventbookingHelper::getCountryCode($row->country ? $row->country : EventbookingHelper::getConfigValue('default_country'));
		if (!$countryCode)
		{
			$countryCode = 'GB';
		}		
		$this->setParam('country', $countryCode);		
		$this->setParam('postcode', $row->zip);
		$this->setParam('tel', $row->phone);
		$this->setParam('email', $row->email);
		$this->submitPost();
	}

	/**
	 * Validate the post data from paypal to our server
	 *
	 * @return string
	 */
	function _validate()
	{
		foreach ($_POST as $key => $value)
		{
			$this->_data[$key] = $value;
		}
		$cartId = JRequest::getVar('cartId', '');
		$amount = JRequest::getVar('amount', '');
		$transId = JRequest::getVar('transId');
		$transStatus = JRequest::getVar('transStatus', '');
		$this->log_ipn_results(true);
		if ($transStatus == 'Y')
		{
			$this->_data['cartId'] = $cartId;
			$this->_data['amount'] = $amount;
			$this->_data['transId'] = $transId;
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Submit post to worldpay server
	 *
	 */	
	function submitPost() {
	?>
		<div class="contentheading"><?php echo  JText::_('EB_WAIT_WORLDPAY'); ?></div>
		<form method="post" action="<?php echo $this->_url; ?>" name="os_form" id="os_form">
			<?php
				foreach ($this->_params as $key=>$val) {
					echo '<input type="hidden" name="'.$key.'" value="'.$val.'" />';
					echo "\n";	
				}
			?>
			<script type="text/javascript">
				function redirect() {
					document.os_form.submit();
				}
				setTimeout('redirect()',5000);
			</script>
		</form>
	<?php	
	}
	
	/**
	 * Log IPN result
	 *
	 * @param string $success
	 */
	function log_ipn_results($success)
	{
		if (!$this->ipn_log)
			return;
		$text = '[' . date('m/d/Y g:i A') . '] - ';
		if ($success)
			$text .= "SUCCESS!\n";
		else
			$text .= 'FAIL: ' . $this->last_error . "\n";
		$text .= "IPN POST Vars from Paypal:\n";
		foreach ($this->_data as $key => $value)
		{
			$text .= "$key=$value, ";
		}
		$text .= "\nIPN Response from Worldpay Server:\n " . $this->ipn_response;
		$fp = fopen($this->ipn_log_file, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp); // close file
	}

	/**
	 * Process payment 
	 *
	 */
	function verifyPayment()
	{
		$ret = $this->_validate();
		if ($ret)
		{
			$config = EventbookingHelper::getConfig();
			$id = $this->_data['cartId'];
			$transactionId = $this->_data['transId'];
			$amount = $this->_data['amount'];
			if ($amount < 0)
			{
				return false;
			}					
			$row = JTable::getInstance('EventBooking', 'Registrant');
			$row->load($id);
			$row->transaction_id = $transactionId;
			$row->payment_date = date('Y-m-d H:i:s');
			$row->published = true;
			$row->store();
			if ($row->is_group_billing)
			{
				EventbookingHelper::updateGroupRegistrationRecord($row->id);
			}
			EventbookingHelper::sendEmails($row, $config);
			JPluginHelper::importPlugin('eventbooking');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onAfterPaymentSuccess', array($row));
			return true;
		}
		else
		{
			return false;
		}
	}
}