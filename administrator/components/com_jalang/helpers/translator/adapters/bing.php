<?php
/**
 * ------------------------------------------------------------------------
 * JA Multilingual Component for Joomla 2.5 & 3.4
 * ------------------------------------------------------------------------
 * Copyright (C) 2004-2011 J.O.O.M Solutions Co., Ltd. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: J.O.O.M Solutions Co., Ltd
 * Websites: http://www.joomlart.com - http://www.joomlancers.com
 * ------------------------------------------------------------------------
 */

defined('_JEXEC') or die;


require_once(dirname(__FILE__) . '/bing/microsoft.php');

class JalangHelperTranslatorBing extends JalangHelperTranslator
{
	/**
	 * constructor
	 *
	 * @param string $from - translate from
	 * @param string $to - translate to
	 * 
	 * @desc Full of Translator Language Codes can be found here
	 * http://msdn.microsoft.com/en-us/library/hh456380.aspx
	 */
	public function __construct($parent, $db, $options = array()) {
		$this->contentType = 'text/html';//text/plain

		parent::__construct();
	}

	public function getServiceToken($reset = false) {
		static $accessToken = null;
		if(!$accessToken || $reset) {
			$clientID     = $this->params->get('bing_client_id', '');
			$clientSecret = $this->params->get('bing_client_secret', '');
			$authUrl      = 'https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/';
			$scopeUrl     = 'http://api.microsofttranslator.com';
			$grantType    = 'client_credentials';

			try{
				//Create the AccessTokenAuthentication object.
				$authObj      = new AccessTokenAuthentication();
				//Get the Access token.
				$accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
			} catch(Exception $e) {
				jexit('<span class="failed">'.$e->getMessage().'</span>');
			}
		}

		return $accessToken;
	}

	public function translate($sentence) {

		$accessToken = $this->getServiceToken();
		//Create the authorization Header string.
		$authHeader = "Authorization: Bearer ". $accessToken;

		//Set the params.
		$options = array(
			'text' => $sentence,
			'from' => $this->from,
			'to' => $this->to,
			'maxTranslations' => '1',
			'options' => array('ContentType' => $this->contentType)
		);


		//HTTP paraphrase URL.
		$paraphraseUrl = "http://api.microsofttranslator.com/V2/Ajax.svc/GetTranslations?" . http_build_query($options);

		try
		{
			//Create the Translator Object.
			$translatorObj = new HTTPTranslator();

			//Call the curlRequest.
			$response = $curlResponse = $translatorObj->curlRequest($paraphraseUrl, $authHeader);
			//{"From":"en","Translations":[{"Count":0,"MatchDegree":100,"MatchedOriginalText":"","Rating":5,"TranslatedText":"translated text"}]}
			$response = preg_replace('/^[^{]*\{/', '{', $response);//The API is returning a wrong byte order mark (BOM).
			$data = json_decode($response);
			if(is_object($data)) {
				return $data->Translations[0]->TranslatedText;
			} else {
				$this->setError($response);
			}
			return false;
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
	
	public function translateArray($sentences, $fields) {
		$accessToken = $this->getServiceToken();
		//Create the authorization Header string.
		$authHeader = "Authorization: Bearer ". $accessToken;

		try
		{
			//Create the Translator Object.
			$translatorObj = new HTTPTranslator();

			//Get the Request XML Format.
			$requestXml = $translatorObj->createReqXML($this->from, $this->to, $this->contentType, $sentences);

			//HTTP TranslateMenthod URL.
			$translateUrl = "http://api.microsofttranslator.com/v2/Http.svc/TranslateArray";

			//Call HTTP Curl Request.
			$curlResponse = $translatorObj->curlRequest($translateUrl, $authHeader, $requestXml);

			//Interprets a string of XML into an object.
			$xmlObj = simplexml_load_string($curlResponse);
			if(is_object($xmlObj)) {
				$i=0;
				$translated = array();
				foreach($xmlObj->TranslateArrayResponse as $translatedArrObj){
					if(isset($translatedArrObj->Error)) {
						$this->setError((string) $translatedArrObj->Error);
						return false;
					}
					$translated[$i++] = (string) $translatedArrObj->TranslatedText;
				}
				if(!count($translated)) {
					$this->setError(nl2br(strip_tags($curlResponse)));
					return false;
				}
				return $translated;
			} else {
				//var_dump($xmlObj);
				$error = preg_replace('/[\s\S]+<body[^>]*>([\s\S]+)<\/body>[\s\S]*/i', '$1', (string) $curlResponse);
				$this->setError($error);
				return false;
			}
		} catch (Exception $e) {
			$this->setError($e->getMessage());
			return false;
		}
	}
}