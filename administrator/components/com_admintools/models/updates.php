<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die;

/**
 * The updates provisioning Model
 */
class AdmintoolsModelUpdates extends F0FUtilsUpdate
{
	/**
	 * Public constructor. Initialises the protected members as well.
	 *
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$isPro = defined('ADMINTOOLS_PRO') ? ADMINTOOLS_PRO : 0;

        $this->componentDescription = 'Admin Tools ' . ($isPro ? 'Professional' : 'Core');

        JLoader::import('joomla.application.component.helper');
        $dlid = F0FUtilsConfigHelper::getComponentConfigurationValue('com_admintools', 'downloadid', '');

		$this->extraQuery = null;

		// If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
		if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $dlid))
		{
			// Even if the user entered a Download ID in the Core version. Let's switch his update channel to Professional
			$isPro = true;

			$this->extraQuery = 'dlid=' . $dlid;
		}

		$this->updateSiteName = 'Admin Tools ' . ($isPro ? 'Professional' : 'Core');

        $this->updateSite = 'http://cdn.akeebabackup.com/updates/at' . ($isPro ? 'pro' : 'core') . '.xml';
	}

    public function autoupdate()
    {
        $return = array(
            'message' => ''
        );

        // First of all let's check if there are any updates
        $updateInfo = (object)$this->getUpdates(true);

        // There are no updates, there's no point in continuing
        if(!$updateInfo->hasUpdate)
        {
            return array(
                'message' => array("No available updates found")
            );
        }

        $return['message'][] = "Update detected, version: ".$updateInfo->version;

        // Ok, an update is found, what should I do?
        $autoupdate = F0FUtilsConfigHelper::getComponentConfigurationValue($this->component, 'autoupdateCli', 1);

        // Let's notifiy the user
        if($autoupdate == 1 || $autoupdate == 2)
        {
            $email = F0FUtilsConfigHelper::getComponentConfigurationValue($this->component, 'notificationEmail');

            if(!$email)
            {
                $return['message'][] = "There isn't an email for notifications, no notification will be sent.";
            }
            else
            {
                // Ok, I can send it out, but before let's check if the user set any frequency limit
                $numfreq    =
                    F0FUtilsConfigHelper::getComponentConfigurationValue($this->component, 'notificationFreq', 1);
                $freqtime   =
                    F0FUtilsConfigHelper::getComponentConfigurationValue($this->component, 'notificationTime', 'day');
                $lastSend   = $this->getLastSend();
                $shouldSend = false;

                if(!$numfreq)
                {
                    $shouldSend = true;
                }
                else
                {
                    $check = strtotime('-'.$numfreq.' '.$freqtime);

                    if($lastSend < $check)
                    {
                        $shouldSend = true;
                    }
                    else
                    {
                        $return['message'][] = "Frequency limit hit, I won't send any email";
                    }
                }

                if($shouldSend)
                {
                    if($this->doSendNotificationEmail($updateInfo->version, $email))
                    {
                        $return['message'][] = "E-mail(s) correctly sent";
                    }
                    else
                    {
                        $return['message'][] = "An error occurred while sending e-mail(s). Please double check your settings";
                    }

                    $this->setLastSend();
                }
            }
        }

        // Let's download and install the latest version
        if($autoupdate == 1 || $autoupdate == 3)
        {
            // DO NOT REMOVE THIS. Since we have a plural class name (AdmintoolsModelCpanels) in a singular file (cpanel.php)
            // we have to do this trick in order to load the correct file
            $t = F0FModel::getTmpInstance('Cpanel', 'AdmintoolsModel');

            if(F0FModel::getTmpInstance('Cpanels', 'AdmintoolsModel')->needsDownloadID())
            {
                $return['message'][] = "You have to enter the DownloadID in order to update your pro version";
            }
            else
            {
                $return['message'][] = $this->doUpdateComponent();
            }
        }

        return $return;
    }

    private function getLastSend()
    {
        $raw = $this->getCommonParameter('lastsend', 0);

        return (int) $raw;
    }

    private function setLastSend()
    {
        $now = time();

        $this->setCommonParameter('lastsend', $now);
    }

    /**
     * Does the user need to provide FTP credentials? It also registers any FTP credentials provided in the URL.
     *
     * @return  bool  True if the user needs to provide FTP credentials
     */
    public function needsFTPCredentials()
    {
        // Determine wether FTP credentials have been passed along with the current request
        JLoader::import('joomla.client.helper');

        $user = $this->input->get('username', null, 'raw');
        $pass = $this->input->get('password', null, 'raw');

        if (!(($user == '') && ($pass == '')))
        {
            // Add credentials to the session
            if (JClientHelper::setCredentials('ftp', $user, $pass))
            {
                return false;
            }

            return true;
        }

        return !JClientHelper::hasCredentials('ftp');
    }
}