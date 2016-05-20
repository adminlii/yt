<?php
/**
 * @author cl
 * @copyright 2013
 */
set_time_limit(50000);
class eBaySoapSession
{
	private $endpoint;
	private $requestToken;
	private $devID;
	private $appID;
	private $certID;
	private $operation;
	private $header;
	
	/**	__construct
		Constructor to make a new instance of eBaySession with the details needed to make a call
		Input:	$userRequestToken - the authentication token fir the user making the call
				$developerID - Developer key obtained when registered at http://developer.ebay.com
				$applicationID - Application key obtained when registered at http://developer.ebay.com
				$certificateID - Certificate key obtained when registered at http://developer.ebay.com
				$useTestServer - Boolean, if true then Sandbox server is used, otherwise production server is used
				$compatabilityLevel - API version this is compatable with
				$siteToUseID - the Id of the eBay site to associate the call iwht (0 = US, 2 = Canada, 3 = UK, ...)
				$callName  - The name of the call being made (e.g. 'GeteBayOfficialTime')
		Output:	Response string returned by the server
	*/
	public function __construct($userRequestToken,$developerID,$applicationID,$certificateID,$endpointUrl,$callName)
	{
		$this->requestToken = $userRequestToken;
		$this->devID = $developerID;
		$this->appID = $applicationID;
		$this->certID = $certificateID;
		$this->endpoint = $endpointUrl;
		$this->operation = $callName;
	}
	
	
	/**	sendHttpRequest
		Sends a HTTP request to the server for this session
		Input:	$requestBody
		Output:	The HTTP Response as a String
	*/
	public function sendHttpRequest($requestBody)
	{
		$this->buildEbayHeaders();
		
		$session = curl_init($this->endpoint);
		
		// create a curl session
		curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($session, CURLOPT_SSL_VERIFYHOST, 0);//--
		curl_setopt($session, CURLOPT_POST, 1); // POST request type
		curl_setopt($session, CURLOPT_POSTFIELDS, $requestBody); // set the body
		curl_setopt($session, CURLOPT_HTTPHEADER,  $this->header);
		curl_setopt($session, CURLOPT_HEADER, 0); // display headers
		curl_setopt($session, CURLOPT_RETURNTRANSFER, 1);
		
		$response = curl_exec($session);
		curl_close($session);
		return $response;
	}
	
	
	
	/**	buildEbayHeaders
		Generates an array of string to be used as the headers for the HTTP request to eBay
		Output:	String Array of Headers applicable for this call
	*/
	private function buildEbayHeaders()
	{
		$headers = array (
		    'X-EBAY-SOA-SERVICE-NAME: ResolutionCaseManagementService',
			'X-EBAY-SOA-OPERATION-NAME: ' .  $this->operation,
			'X-EBAY-SOA-SERVICE-VERSION: 1.3.0',
		    'X-EBAY-SOA-SECURITY-TOKEN: ' .  $this->requestToken,
		    'X-EBAY-SOA-REQUEST-DATA-FORMAT: XML',
		    
		    'X-EBAY-API-DEV-NAME: ' .  $this->devID,
		    'X-EBAY-API-APP-NAME: ' .  $this->appID,
		    'X-EBAY-API-CERT-NAME: ' .  $this->certID,
		);
		$this->header = $headers;
		return $headers;
	}
	

	/**	buildEbayHeaders
	 Generates an array of string to be used as the headers for the HTTP request to eBay
	 Output:	String Array of Headers applicable for this call
	 */
	private  function getEbayHeaders()
	{		
		return $this->header;
	}

}
?>