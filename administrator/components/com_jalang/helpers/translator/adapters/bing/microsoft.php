<?php
/**
 * Microsoft Limited Public License
 * 
 * This license governs use of code marked as "sample" or "example" available on this web site without a license agreement,
 * as provided under the section above titled "NOTICE SPECIFIC TO SOFTWARE AVAILABLE ON THIS WEB SITE."
 * If you use such code (the "software"), you accept this license. If you do not accept the license, do not use the software.
 * 
 * Definitions
 * 
 * The terms "reproduce," "reproduction," "derivative works," and "distribution" have the same meaning here as under U.S. copyright law.
 * 
 * A "contribution" is the original software, or any additions or changes to the software.
 * 
 * A "contributor" is any person that distributes its contribution under this license.
 * 
 * "Licensed patents" are a contributor's patent claims that read directly on its contribution.
 * Grant of Rights
 * 	Copyright Grant - Subject to the terms of this license, including the license conditions and limitations in section 3,
 * 		each contributor grants you a non-exclusive, worldwide, royalty-free copyright license to reproduce its contribution,
 * 		prepare derivative works of its contribution, and distribute its contribution or any derivative works that you create.
 * 	Patent Grant - Subject to the terms of this license, including the license conditions and limitations in section 3,
 * 		each contributor grants you a non-exclusive, worldwide, royalty-free license under its licensed patents to make,
 * 		have made, use, sell, offer for sale, import, and/or otherwise dispose of its contribution in the software or derivative works of the contribution in the software.
 * Conditions and Limitations
 * 	No Trademark License- This license does not grant you rights to use any contributors' name, logo, or trademarks.
 * 	If you bring a patent claim against any contributor over patents that you claim are infringed by the software, your patent license from such contributor to the software ends automatically.
 * 	If you distribute any portion of the software, you must retain all copyright, patent, trademark, and attribution notices that are present in the software.
 * 	If you distribute any portion of the software in source code form, you may do so only under this license by including a complete copy of this license with your distribution.  If you distribute any portion of the software in compiled or object code form, you may only do so under a license that complies with this license.
 * 	The software is licensed "as-is." You bear the risk of using it. The contributors give no express warranties, guarantees or conditions.  You may have additional consumer rights under your local laws which this license cannot change. To the extent permitted under your local laws, the contributors exclude the implied warranties of merchantability, fitness for a particular purpose and non-infringement.
 * 	Platform Limitation - The licenses granted in sections 2(A) and 2(B) extend only to the software or derivative works that you create that run on a Microsoft Windows operating system product.
 */

defined('_JEXEC') or die;

class AccessTokenAuthentication {
    /*
     * Get the access token.
     *
     * @param string $grantType    Grant type.
     * @param string $scopeUrl     Application Scope URL.
     * @param string $clientID     Application client ID.
     * @param string $clientSecret Application client ID.
     * @param string $authUrl      Oauth Url.
     *
     * @return string.
     */
    function getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl){
		//Initialize the Curl Session.
		$ch = curl_init();
		//Create the request Array.
		$paramArr = array (
			'grant_type'    => $grantType,
			'scope'         => $scopeUrl,
			'client_id'     => $clientID,
			'client_secret' => $clientSecret
		);
		//Create an Http Query.//
		$paramArr = http_build_query($paramArr);
		//Set the Curl URL.
		curl_setopt($ch, CURLOPT_URL, $authUrl);
		//Set HTTP POST Request.
		curl_setopt($ch, CURLOPT_POST, TRUE);
		//Set data to POST in HTTP "POST" Operation.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $paramArr);
		//CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
		//CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		//Execute the  cURL session.
		$strResponse = curl_exec($ch);
		//Get the Error Code returned by Curl.
		$curlErrno = curl_errno($ch);
		if($curlErrno){
			$curlError = curl_error($ch);
			throw new Exception($curlError);
		}
		//Close the Curl Session.
		curl_close($ch);
		//Decode the returned JSON string.
		$objResponse = json_decode($strResponse);
		if (isset($objResponse->error)){
			throw new Exception($objResponse->error_description);
		}
		return $objResponse->access_token;
    }
}

/*
 * Class:HTTPTranslator
 *
 * Processing the translator request.
 */
Class HTTPTranslator {
    /*
     * Create and execute the HTTP CURL request.
     *
     * @param string $url        HTTP Url.
     * @param string $authHeader Authorization Header string.
     * @param string $postData   Data to post.
     *
     * @return string.
     *
     */
    function curlRequest($url, $authHeader, $postData=''){
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt ($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array($authHeader,"Content-Type: text/xml"));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
        if($postData) {
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        //Execute the  cURL session.
        $curlResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        return $curlResponse;
    }


    /*
     * Create Request XML Format.
     *
     * @param string $fromLanguage   Source language Code.
     * @param string $toLanguage     Target language Code.
     * @param string $contentType    Content Type.
     * @param string $inputStrArr    Input String Array.
     *
     * @return string.
     */
    function createReqXML($fromLanguage,$toLanguage,$contentType,$inputStrArr) {
        //Create the XML string for passing the values.
        $requestXml = "<TranslateArrayRequest>".
            "<AppId/>".
            "<From>$fromLanguage</From>". 
            "<Options>" .
             "<Category xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<ContentType xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\">$contentType</ContentType>" .
              "<ReservedFlags xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<State xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<Uri xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
              "<User xmlns=\"http://schemas.datacontract.org/2004/07/Microsoft.MT.Web.Service.V2\" />" .
            "</Options>" .
            "<Texts>";
        foreach ($inputStrArr as $inputStr)
        $requestXml .=  "<string xmlns=\"http://schemas.microsoft.com/2003/10/Serialization/Arrays\"><![CDATA[$inputStr]]></string>" ;
        $requestXml .= "</Texts>".
            "<To>$toLanguage</To>" .
          "</TranslateArrayRequest>";
        return $requestXml;
    }
}

/**
 * Sample Code
 * //Client ID of the application.
    $clientID       = "clientId";
    //Client Secret key of the application.
    $clientSecret = "ClientSecret";
    //OAuth Url.
    $authUrl      = "https://datamarket.accesscontrol.windows.net/v2/OAuth2-13/";
    //Application Scope Url
    $scopeUrl     = "http://api.microsofttranslator.com";
    //Application grant type
    $grantType    = "client_credentials";

    //Create the AccessTokenAuthentication object.
    $authObj      = new AccessTokenAuthentication();
    //Get the Access token.
    $accessToken  = $authObj->getTokens($grantType, $scopeUrl, $clientID, $clientSecret, $authUrl);
    //Create the authorization Header string.
    $authHeader = "Authorization: Bearer ". $accessToken;

    //Set the params.
    $sentence      = "rephrasing is a hard problem for the computer.";
    $language      = "en-us";
    $category        = "general";
    $maxParaphrase = '6';
    
    $params = "sentence=".urlencode($sentence)."&language=$language&category=$category&maxParaphrases=$maxParaphrase";
    
    //HTTP paraphrase URL.
    $paraphraseUrl = "http://api.microsofttranslator.com/v3/json/paraphrase?$params";
    
    //Create the Translator Object.
    $translatorObj = new HTTPTranslator();
    
    //Call the curlRequest.
    echo $curlResponse = $translatorObj->curlRequest($paraphraseUrl, $authHeader);
 */