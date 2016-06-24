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
class OSMembershipHelperEuvat
{
	public static $europeanUnionVATInformation = array(
		// Core member states
		'BE' => array('Belgium', 'BE', 21),
		'BG' => array('Bulgaria', 'BG', 20),
		'CZ' => array('Czech Rebulic', 'CZ', 21),
		'DK' => array('Denmark', 'DK', 25),
		'DE' => array('Germany', 'DE', 19),
		'EE' => array('Estonia', 'EE', 20),
		'GR' => array('Greece', 'EL', 23),
		'ES' => array('Spain', 'ES', 21),
		'FR' => array('France', 'FR', 20),
		'HR' => array('Croatia', 'HR', 25),
		'IE' => array('Ireland', 'IE', 23),
		'IT' => array('Italy', 'IT', 22),
		'CY' => array('Cyprus', 'CY', 19),
		'LV' => array('Latvia', 'LV', 21),
		'LT' => array('Lithuania', 'LT', 21),
		'LU' => array('Luxembourg', 'LU', 15),
		'HU' => array('Hungary', 'HU', 27),
		'MT' => array('Malta', 'MT', 18),
		'NL' => array('Netherlands', 'NL', 21),
		'AT' => array('Austria', 'AT', 20),
		'PL' => array('Poland', 'PL', 23),
		'PT' => array('Portugal', 'PT', 23),
		'RO' => array('Romania', 'RO', 24),
		'SI' => array('Slovenia', 'SI', 22),
		'SK' => array('Slovakia', 'SK', 20),
		'FI' => array('Finland', 'FI', 24),
		'SE' => array('Sweden', 'SE', 25),
		'GB' => array('United Kingdom', 'GB', 20),
		// Special cases of countries which belong to a core member state for VAT calculation
		'MC' => array('Monaco', 'FR', 20), // Monaco -> France
		'IM' => array('Isle of Man', 'GB', 20), // Isle of Man -> Great Britain
	);

	/**
	 * Method to check to see whether the given country belongs to EU
	 *
	 * @param $countryCode
	 *
	 * @return bool
	 */
	public static function isEUCountry($countryCode)
	{
		$countryCode = strtoupper($countryCode);

		return array_key_exists($countryCode, self::$europeanUnionVATInformation);
	}

	/**
	 * Get the tax rate of the given EU Country
	 *
	 * @param $countryCode
	 *
	 * @return float
	 */
	public static function getEUCountryTaxRate($countryCode)
	{
		$countryCode = strtoupper($countryCode);
		if (isset(self::$europeanUnionVATInformation[$countryCode]))
		{
			return self::$europeanUnionVATInformation[$countryCode][2];
		}

		return 0;
	}

	/**
	 * Check if the given VAT Number is in valid format or not
	 *
	 * @param $id
	 *
	 * @return bool
	 */
	public static function preCheckVatNumber($id)
	{
		$id = strtoupper($id);
		$id = preg_replace('/[ -,.]/', '', $id);
		if (strlen($id) < 8)
		{
			return false;
		}
		$country = substr($id, 0, 2);
		switch ($country)
		{
			case 'AT': // AUSTRIA
				$isValid = (bool) preg_match('/^(AT)U(\d{8})$/', $id);
				break;
			case 'BE': // BELGIUM
				$isValid = (bool) preg_match('/(BE)(0?\d{9})$/', $id);
				break;
			case 'BG': // BULGARIA
				$isValid = (bool) preg_match('/(BG)(\d{9,10})$/', $id);
				break;
			case 'CHE': // Switzerland
				$isValid = (bool) preg_match('/(CHE)(\d{9})(MWST)?$/', $id);
				break;
			case 'CY': // CYPRUS
				$isValid = (bool) preg_match('/^(CY)([0-5|9]\d{7}[A-Z])$/', $id);
				break;
			case 'CZ': // CZECH REPUBLIC
				$isValid = (bool) preg_match('/^(CZ)(\d{8,10})(\d{3})?$/', $id);
				break;
			case 'DE': // GERMANY
				$isValid = (bool) preg_match('/^(DE)([1-9]\d{8})/', $id);
				break;
			case 'DK': // DENMARK
				$isValid = (bool) preg_match('/^(DK)(\d{8})$/', $id);
				break;
			case 'EE': // ESTONIA
				$isValid = (bool) preg_match('/^(EE)(10\d{7})$/', $id);
				break;
			case 'EL': // GREECE
				$isValid = (bool) preg_match('/^(EL)(\d{9})$/', $id);
				break;
			case 'ES': // SPAIN
				$isValid = (bool) preg_match('/^(ES)([A-Z]\d{8})$/', $id)
					|| preg_match('/^(ES)([A-H|N-S|W]\d{7}[A-J])$/', $id)
					|| preg_match('/^(ES)([0-9|Y|Z]\d{7}[A-Z])$/', $id)
					|| preg_match('/^(ES)([K|L|M|X]\d{7}[A-Z])$/', $id);
				break;
			case 'EU': // EU type
				$isValid = (bool) preg_match('/^(EU)(\d{9})$/', $id);
				break;
			case 'FI': // FINLAND
				$isValid = (bool) preg_match('/^(FI)(\d{8})$/', $id);
				break;
			case 'FR': // FRANCE
				$isValid = (bool) preg_match('/^(FR)(\d{11})$/', $id)
					|| preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]\d{10})$/', $id)
					|| preg_match('/^(FR)(\d[(A-H)|(J-N)|(P-Z)]\d{9})$/', $id)
					|| preg_match('/^(FR)([(A-H)|(J-N)|(P-Z)]{2}\d{9})$/', $id);
				break;
			case 'GB': // GREAT BRITAIN
				$isValid = (bool) preg_match('/^(GB)?(\d{9})$/', $id)
					|| preg_match('/^(GB)?(\d{12})$/', $id)
					|| preg_match('/^(GB)?(GD\d{3})$/', $id)
					|| preg_match('/^(GB)?(HA\d{3})$/', $id);
				break;
			case 'GR': // GREECE
				$isValid = (bool) preg_match('/^(GR)(\d{8,9})$/', $id);
				break;
			case 'HR': // CROATIA
				$isValid = (bool) preg_match('/^(HR)(\d{11})$/', $id);
				break;
			case 'HU': // HUNGARY
				$isValid = (bool) preg_match('/^(HU)(\d{8})$/', $id);
				break;
			case 'IE': // IRELAND
				$isValid = (bool) preg_match('/^(IE)(\d{7}[A-W])$/', $id)
					|| preg_match('/^(IE)([7-9][A-Z\*\+)]\d{5}[A-W])$/', $id)
					|| preg_match('/^(IE)(\d{7}[A-W][AH])$/', $id);
				break;
			case 'IT': // ITALY
				$isValid = (bool) preg_match('/^(IT)(\d{11})$/', $id);
				break;
			case 'LV': // LATVIA
				$isValid = (bool) preg_match('/^(LV)(\d{11})$/', $id);
				break;
			case 'LT': // LITHUNIA
				$isValid = (bool) preg_match('/^(LT)(\d{9}|\d{12})$/', $id);
				break;
			case 'LU': // LUXEMBOURG
				$isValid = (bool) preg_match('/^(LU)(\d{8})$/', $id);
				break;
			case 'MT': // MALTA
				$isValid = (bool) preg_match('/^(MT)([1-9]\d{7})$/', $id);
				break;
			case 'NL': // NETHERLAND
				$isValid = (bool) preg_match('/^(NL)(\d{9})B\d{2}$/', $id);
				break;
			case 'NO': // NORWAY
				$isValid = (bool) preg_match('/^(NO)(\d{9})$/', $id);
				break;
			case 'PL': // POLAND
				$isValid = (bool) preg_match('/^(PL)(\d{10})$/', $id);
				break;
			case 'PT': // PORTUGAL
				$isValid = (bool) preg_match('/^(PT)(\d{9})$/', $id);
				break;
			case 'RO': // ROMANIA
				$isValid = (bool) preg_match('/^(RO)([1-9]\d{1,9})$/', $id);
				break;
			case 'RS': // SERBIA
				$isValid = (bool) preg_match('/^(RS)(\d{9})$/', $id);
				break;
			case 'SI': // SLOVENIA
				$isValid = (bool) preg_match('/^(SI)([1-9]\d{7})$/', $id);
				break;
			case 'SK': // SLOVAK REPUBLIC
				$isValid = (bool) preg_match('/^(SK)([1-9]\d[(2-4)|(6-9)]\d{7})$/', $id);
				break;
			case 'SE': // SWEDEN
				$isValid = (bool) preg_match('/^(SE)(\d{10}01)$/', $id);
				break;
			default:
				$isValid = false;
		}

		return $isValid;
	}

	/**
	 * Validate EU VAT Number
	 *
	 * @param $vatNumber
	 *
	 * @return bool
	 */
	public static function validateEUVATNumber($vatNumber)
	{
		// Remove spaces
		$vatNumber = preg_replace('/\s+/', '', $vatNumber);

		// Check to see if the VAT Number passed is in valid format before calling webservices
		if (!self::preCheckVatNumber($vatNumber))
		{
			return false;
		}

		// Use web service to validate the VAT number
		$countryCode = substr($vatNumber, 0, 2);
		$number      = substr($vatNumber, 2);
		if (class_exists('SoapClient'))
		{
			$client = new SoapClient("http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl");
			$rs     = $client->checkVat(array(
					'countryCode' => $countryCode,
					'vatNumber'   => $number)
			);

			return $rs->valid;
		}
		else
		{
			$url = "http://ec.europa.eu/taxation_customs/vies/viesquer.do?ms=" . $countryCode . "&vat=" . $number;
			$ch  = curl_init($url);
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			$resp = curl_exec($ch);
			curl_close($ch);
			if (strpos($resp, '="validStyle"') !== false)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
	}
}