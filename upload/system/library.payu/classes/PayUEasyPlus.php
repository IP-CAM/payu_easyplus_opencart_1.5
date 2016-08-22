<?php

/**
 * This file contains the class for doign PayuRedirectPaymentPage transactions
 * Date:
 * 
 * @version 1.0
 * 
 * 
 */

//Requiring the PayU base class
require_once('PayUBase.php');

class PayUEasyPlus extends PayUBase {
    
    public function __construct($config = array()) 
    {
        //instantiate parent
        parent::__construct($config);
        
        //Setting the base url
        $this->payuBaseUrlToUse = $this->payuUrl['staging'];
        
        //overriding several initialisation values (id specified)
        if(isset($config['production']) && ($config['production'] != '') ) {
            $this->payuBaseUrlToUse = $this->payuUrl['production'];
        }        
        if(isset($config['api_username']) && (!empty($config['api_username'])) ) {
            $this->merchantSoapUsername = $config['api_username'];
        }        
        if(isset($config['api_password']) && (!empty($config['api_password'])) ) {
            $this->merchantSoapPassword = $config['api_password'];
        }
        if(isset($config['extended_debug']) && $config['extended_debug'] ) {
            $this->extendedDebug = true;
        }
        if(isset($config['safe_key']) && (!empty($config['safe_key'])) ) {
            $this->safeKey = $config['safe_key'];
        }        
        
        //Setting the neccesary variables used in the class
        $this->setSoapWdslUrl();         
    }
    
    /**    
    *
    * Do the get transaction soap call against the PayU API and returns a url containing the RPP url with reference
    *    
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the get transaction response details
    */
    public function doGetTransaction($soapData = array()) 
    {
        $returnData = $this->doSoapCallToApi('getTransaction', $soapData);    
        
        $return = array();
        $return['soap_response'] = $returnData['return'];
        //$return['payu_easyplus_url'] = $this->getTransactionRedirectPageUrl($returnData['return']['PayUReference']);
        
        return $return;
    }
    
    
    /**    
    *
    * Do the set transaction soap call against the PayU API and returns a url containing the RPP url with reference
    *
    * @param string soapFunctionToCall The Soap function the needs to be called
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the set transaction response details
    */
    public function doSetTransaction($soapDataArray = array()) 
    {        
        $returnData = $this->doSoapCallToApi('setTransaction', $soapDataArray);        
        
        
        //If succesfull then pass back the payUreference  with return URL
        if(isset($returnData['return']['successful']) && ($returnData['return']['successful'] === true) ) {
            $temp = array();
            $temp['soap_response'] = $returnData['return'];
            $temp['payu_easyplus_url'] = $this->getTransactionRedirectPageUrl($returnData['return']['payUReference']);
            
            return $temp;
        } else {
            $returnData['soap_response'] = $returnData['return'];
            
            $errorMessage = "Unspecified error. please contact merchant";
            if($this->extendedDebug === true) {
                $errorMessage = $returnData['soap_response']['displayMessage'] . ", Details: " . $errorMessage = $returnData['soap_response']['resultMessage'];
            }
            elseif(isset($returnData['soap_response']['displayMessage']) && !empty($returnData['soap_response']['displayMessage']) ) {
                $errorMessage = $returnData['soap_response']['displayMessage'];
            }
            elseif(isset($returnData['soap_response']['resultMessage']) && !empty($returnData['soap_response']['resultMessage']) ) {
                $errorMessage = $returnData['soap_response']['resultMessage'];
            }             
            return $errorMessage;
        }
        
    }
    
    /**    
    *
    * Do the soap call against the PayU API
    *
    * @param string soapFunctionToCall The Soap function the needs to be called
    * @param array soapDataArray The array containing the data to
    *
    * @return array Returns the soap result in array format
    */
    public function doSoapCallToApi($functionToCall = null , $soapData = array()) 
    {
        
        // A couple of validation business ruless before doing the soap call
        if(empty($soapData)) {
            throw new Exception("Please provide data to be used on the soap call");
        }
        elseif(empty($functionToCall)) {
            throw new Exception("Please provide a soap function to call");
        }

        //Setting the soap header if not already set
        if(empty($this->soapAuthHeader)) {
            $this->setSoapHeader();
        }
        
        //Setting the soap header if not already set
        if(!empty($this->safeKey)) {            
            $soapData['Safekey'] = $this->safeKey;
        }
        
        //log an entry indicating that a soap call is about to happen
        $this->log("---SOAP CALL TRANSACTION ABOUT TO START: ".$functionToCall."   ---\r\n");
        
                
        //Make new instance of the PHP Soap client
        $this->soapClientInstance = new SoapClient($this->soapWdslUrl, array("trace" => 1, "exception" => 0)); 

        //Set the Headers of soap client. 
        $this->soapClientInstance->__setSoapHeaders($this->soapAuthHeader); 

        //Adding the api version to the soap data packet array
        $soapData = array_merge($soapData, array('Api' => $this->apiVersion ));
        
       
        //Do Soap call
        try {
            $soapCallResult = $this->soapClientInstance->$functionToCall($soapData); 
            if(is_object($this->soapClientInstance)) {       
                $this->log("SOAP CALL RESPONSE HEADERS: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastResponseHeaders());
                $this->log("SOAP CALL RESPONSE: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastResponse()."\r\n");        
            }
        } catch(Exception $e) {
            if(is_object($this->soapClientInstance)) {
                $this->log("SOAP CALL REQUEST HEADERS: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastRequestHeaders());
                $this->log("SOAP CALL REQUEST: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastRequest());        
                $this->log("SOAP CALL RESPONSE HEADERS: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastResponseHeaders());
                $this->log("SOAP CALL RESPONSE: ".$functionToCall, "\r\n".$this->soapClientInstance->__getLastResponse()."\r\n");        
            }
            throw new Exception($e->getMessage(),null,$e);
        }        
        // Decode the Soap Call Result for returning
        $returnData = json_decode(json_encode($soapCallResult),true);

        return $returnData;
    }
    
    /**    
     * Set the soap header string used to call in the Soap to PayU API
     */        
    private function setSoapHeader() 
    {
        if(empty($this->merchantSoapUsername)) {
            throw new exception('Please specify a merchant username for soap trasaction');
        }
        elseif(empty($this->merchantSoapPassword)) {
            throw new exception('Please specify a merchant password for soap trasaction');
        }
        
        //Creating a soap xml
        $headerXml = '<wsse:Security SOAP-ENV:mustUnderstand="1" xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd">';
        $headerXml .= '<wsse:UsernameToken wsu:Id="UsernameToken-9" xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd">';
        $headerXml .= '<wsse:Username>'.$this->merchantSoapUsername.'</wsse:Username>';
        $headerXml .= '<wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">'.$this->merchantSoapPassword.'</wsse:Password>';
        $headerXml .= '</wsse:UsernameToken>';
        $headerXml .= '</wsse:Security>';
        $headerbody = new SoapVar($headerXml, XSD_ANYXML, null, null, null);  
        
        $ns = 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd'; //Namespace of the WS.         
        $this->soapAuthHeader = new SOAPHeader($ns, 'Security', $headerbody, true);        
    }
    
    
    /*     
     * Set the Base RPP Url to use      
     */        
    private function getTransactionRedirectPageUrl($payuReference = null) 
    {
        if(empty($payuReference)) {
            throw new Exception('Please specify a valid PayU Reference number');
        }
        return $this->payuBaseUrlToUse.'/rpp.do?PayUReference='.$payuReference;
    }

    /**    
     * Set the PayU soap WDSL URL for use in soap
     */        
    private function setSoapWdslUrl() 
    {
        $this->soapWdslUrl = $this->payuBaseUrlToUse.'/service/PayUAPI?wsdl';        
    }

}

