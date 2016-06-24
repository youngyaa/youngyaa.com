<?php
/**
 * Part of the Ossolution Payment Package
 *
 * @copyright  Copyright (C) 2015 - 2016 Ossolution Team. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

/**
 * Abstract Payment Class
 *
 * @since  1.0
 */
abstract class MPFPayment
{
	/**
	 * The name of payment method
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	protected $name;

	/**
	 * The title of payment method
	 *
	 * @var string
	 *
	 * @since 1.0
	 */
	public $title;

	/**
	 * Payment method type
	 *
	 * @var int 0: off-site (redirect), 1: on-site (credit card)
	 */
	protected $type = 0;

	/***
	 * Payment mode
	 *
	 * @var bool
	 *
	 * @since 1.0
	 */
	protected $mode;

	/***
	 * Payment gateway URL
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Payment plugin parameters
	 *
	 * @var \Joomla\Registry\Registry
	 */
	protected $params;

	/**
	 * The parameters which will be passed to payment gateway for processing payment
	 *
	 * @var array
	 */
	protected $parameters = array();

	/**
	 * Notification data send from payment gateway back to the payment plugin.
	 *
	 * @var array
	 */
	protected $notificationData = null;

	/**
	 * Payment Fee
	 *
	 * @var bool
	 */
	public $paymentFee = false;

	/**
	 * Instantiate the payment object
	 *
	 * @param \Joomla\Registry\Registry $params
	 * @param array                     $config
	 */
	public function __construct($params, $config = array())
	{
		$this->name = get_class($this);

		$this->mode = $params->get('mode', 0);

		if (isset($config['type']))
		{
			$this->type = (int) $config['type'];
		}

		$this->params = $params;
	}

	/**
	 *
	 * Set data for a parameter
	 *
	 * @param string $name
	 * @param string $value
	 */
	protected function setParameter($name, $value)
	{
		$this->parameters[$name] = $value;
	}

	/**
	 * Get data for a parameter
	 *
	 * @param  string $name
	 * @param  mixed  $default
	 *
	 * @return null
	 */
	protected function getParameter($name, $default = null)
	{
		return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
	}

	/**
	 * This is the main method of the payment gateway. It get the data which users input and the calculated payment
	 * amount, pass to payment gateway for processing payment
	 *
	 * @param $row
	 * @param $data
	 */

	abstract public function processPayment($row, $data);

	/**
	 * Get name of the payment method
	 *
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Get title of the payment method
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Set title of the payment method
	 *
	 * @param $title String
	 */

	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * Method to check if this payment method is a CreditCard based payment method
	 *
	 * @return int
	 */
	public function getCreditCard()
	{
		return $this->type;
	}

	/**
	 * Method to check whether we need to show card type on form for this payment method. From now on, we don't have to
	 * show card type on form because it can be detected from card number. Keep it here for B/C reason only
	 *
	 * @return bool|int
	 */
	public function getCardType()
	{
		return 0;
	}

	/**
	 * Method to check whether we need to show card cvv in the form for this payment method
	 *
	 * @return int
	 */
	public function getCardCvv()
	{
		return $this->type;
	}

	/**
	 * Method to check whether we need to show card holder name in the form. For credit card base payment method, we
	 * always show this field
	 *
	 * @return bool|int
	 */
	public function getCardHolderName()
	{
		return $this->type;
	}

	/**
	 *  This method is called when payment for the registration is success, it needs to be used by all payment class
	 *
	 * @param JTable $row
	 * @param string $transactionId
	 */
	protected function onPaymentSuccess($row, $transactionId)
	{
		$config              = OSMembershipHelper::getConfig();
		$row->transaction_id = $transactionId;
		$row->payment_date   = gmdate('Y-m-d H:i:s');
		$row->published      = 1;
		$row->store();
		if ($row->act == 'upgrade')
		{
			OSMembershipHelper::processUpgradeMembership($row);
		}
		JPluginHelper::importPlugin('osmembership');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onMembershipActive', array($row));
		OSMembershipHelper::sendEmails($row, $config);
	}

	/***
	 * Render form which will redirect users to payment gateway for processing payment
	 *
	 * @param string $url The payment gateway URL which users will be redirected to
	 * @param        $data
	 *
	 * @return void
	 *
	 * @since 1.0
	 */
	protected function renderRedirectForm($url = null, $data = array())
	{
		if (empty($url))
		{
			$url = $this->url;
		}

		if (empty($data))
		{
			$data = $this->parameters;
		}

		//Get redirect heading
		$language    = JFactory::getLanguage();
		$languageKey = 'OSM_WAIT_' . strtoupper(substr($this->name, 3));
		if ($language->hasKey($languageKey))
		{
			$redirectHeading = JText::_($languageKey);
		}
		else
		{
			$redirectHeading = JText::sprintf('OSM_REDIRECT_HEADING', $this->getTitle());
		}
		?>
		<div class="payment-heading"><?php echo $redirectHeading; ?></div>
		<form method="post" action="<?php echo $url; ?>" name="payment_form" id="payment_form">
			<?php
			foreach ($data as $key => $val)
			{
				echo '<input type="hidden" name="' . $key . '" value="' . $val . '" />';
				echo "\n";
			}
			?>
			<script type="text/javascript">
				function redirect() {
					document.payment_form.submit();
				}
				setTimeout('redirect()', 5000);
			</script>
		</form>
	<?php
	}

	/***
	 * Log the notification data
	 *
	 * @param string $extraData a string contain the extra data which you want to log
	 *
	 * @return void
	 *
	 *
	 */
	protected function logGatewayData($extraData = null)
	{
		if (!$this->params->get('ipn_log'))
		{
			return;
		}

		$text = '[' . date('m/d/Y g:i A') . '] - ';
		$text .= "Notification Data From : " . $this->title . " \n";
		foreach ($this->notificationData as $key => $value)
		{
			$text .= "$key=$value, ";
		}

		$text .= $extraData;

		$ipnLogFile = JPATH_COMPONENT . '/ipn_' . $this->getName() . '.txt';
		$fp         = fopen($ipnLogFile, 'a');
		fwrite($fp, $text . "\n\n");
		fclose($fp);
	}
}