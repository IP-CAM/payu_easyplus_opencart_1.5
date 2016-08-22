<?php
require (preg_replace("/\/+/","/", dirname(__FILE__)."/./../../../system/library.payu/config.payu-easyplus.php"));

class ControllerPaymentPayueasyplus extends Controller {
	public function index() 
    {
        $this->load->language('payment/payu_easyplus');

        $this->data['button_pay'] = $this->language->get('button_confirm');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payu_easyplus_confirm.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payu_easyplus_confirm.tpl';
        } else {
            $this->template = 'default/template/payment/payu_easyplus_confirm.tpl';
        }

        $this->render();
	}
    
    public function send () 
    {
		$this->load->model('checkout/order');
		$this->language->load('payment/payu_easyplus');
		
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        
        $setTransactionData = array();
        $setTransactionData['TransactionType'] = $this->config->get('payu_easyplus_transaction_type');
        
        // Creating Basket Array
        $basket = array();
        $basket['amountInCents'] = $order_info['total']*100;
		if (strpos($basket['amountInCents'],'.') !== false) {
			list($basket['amountInCents'],$tempVar) = explode(".", $basket['amountInCents'], 2);			
			$basket['amountInCents'] = $basket['amountInCents']+1;
		}
        $basket['description'] = 'Order ID:' . $order_info['order_id'];;
        $basket['currencyCode'] = $this->config->get('payu_easyplus_payment_currency');
        $setTransactionData = array_merge($setTransactionData, array('Basket' => $basket));
        $basket = null; 
        unset($basket);

        // Creating Customer Array
        $customer = array();
        $customer['firstName'] = $order_info['payment_firstname'];
        $customer['lastName'] = $order_info['payment_lastname'];
        $customer['mobile'] = $order_info['telephone'];
        $customer['email'] = $order_info['email'];        
        $customer['ip'] = $this->getRealClientIPAddress();
        $setTransactionDataArray = array_merge($setTransactionData, array('Customer' => $customer));
        $customer = null; 
        unset($customer);
        
        //Creating Additional Information Array
        $additionalInformation = array();
        $additionalInformation['supportedPaymentMethods'] = $this->config->get('payu_easyplus_payment_methods');
        $additionalInformation['cancelUrl'] = $this->config->get('payu_easyplus_cancel_url' );
        $additionalInformation['notificationUrl'] = $this->config->get('payu_easyplus_ipn_url' );
        $additionalInformation['returnUrl'] = $this->config->get('payu_easyplus_return_url');
        $additionalInformation['merchantReference'] = $order_info['order_id'];
        $setTransactionData = array_merge($setTransactionData, array('AdditionalInformation' => $additionalInformation));
        $additionalInformation = null; 
        unset($additionalInformation);
        
        //Creating a config array for RPP instantiation        
        $config = array();        
        $config['safe_key'] = $this->config->get('payu_easyplus_safe_key'); ;
        $config['api_username'] = $this->config->get('payu_easyplus_api_username'); ;
        $config['api_password'] = $this->config->get('payu_easyplus_api_password'); ;;
        
        $config['logEnable'] = true;
        $config['extended_debug'] = true;
        
        if(strtolower($this->config->get('payu_easyplus_transaction_mode')) == 'production') {
            $config['production'] = true;
            $config['logEnable'] = false;
            $config['extended_debug'] = false;
        }

        $json['error'] = 'Unable to contact PayU service. Please contact merchant.';
        $message = '';
        try{    
            $payUEasyPlus = new PayUEasyPlus($config);
            $setTransactionResponse = $payUEasyPlus->doSetTransaction($setTransactionData);
            if(isset($setTransactionResponse['payu_easyplus_url'])) {
                $json['redirect'] = $setTransactionResponse['payu_easyplus_url'];
                $status_id = $this->config->get('payu_easyplus_order_status_id');
                $message = 'Redirected to PayU for payment, ';            
                $message .= 'PayU Reference: ' . $setTransactionResponse['soap_response']['payUReference'];
                $this->model_checkout_order->confirm($this->session->data['order_id'], $status_id, $message, true);
            } else {
                $this->session->data['error'] = $json['error'];
                if($this->config->get('payu_easyplus_extended_debug')) {
                    $this->log->write(serialize($setTransactionResponse));
                }

                $this->redirect($this->url->link('checkout/checkout')); 
            }  
        } catch(Exception $e) {
            $json['error'] = $e->getMessage();
        }

        if(isset($json['redirect'])) {
            unset($json['error']);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
	
    public function response() 
    {
        $this->load->model('checkout/order');

        $transactionState = 'failure';    
        try {
            $message = '';
            
            if(!empty($this->request->get['PayUReference'])) {
                //Creating get transaction soap data array
                $getTransactionData = array();
                $getTransactionData['AdditionalInformation']['payUReference'] = $this->request->get['PayUReference'];        
                $config = array();        
                $config['safe_key'] = $this->config->get('payu_easyplus_safe_key');
                $config['api_username'] = $this->config->get('payu_easyplus_api_username');
                $config['api_password'] = $this->config->get('payu_easyplus_api_password');
                $config['logEnable'] = $this->config->get('payu_easyplus_api_password');
                $config['extended_debug'] = $this->config->get('payu_easyplus_api_password');
                if($this->config->get('payu_easylus_transaction_mode') == 'production') {
                    $config['production'] = true;
                }

                $payUEasyPlus = new PayUEasyPlus($config);
                $response = $payUEasyPlus->doGetTransaction($getTransactionData); 
                //var_dump();
                //exit;
                $message = $response['soap_response']['displayMessage'];
                
                //Checking the response from the SOAP call to see if successfull
                if(isset($response['soap_response']['successful']) 
                    && $response['soap_response']['successful']) 
                {                    
                    if(isset($response['soap_response']['transactionType']) 
                        && $response['soap_response']['transactionType'] == $this->config->get('payu_easyplus_transaction_type')) 
                    {                    
                        $MerchantReferenceCheck = $this->session->data['order_id'];
                        $MerchantReferenceCallBack = $response['soap_response']['merchantReference'];
                        $gatewayReference = $response['soap_response']['paymentMethodsUsed']['gatewayReference'];
                        $transactionState = 'paymentSuccessfull';
                    }                    
                } else {
                    $message = $response['soap_response']['displayMessage'];
                }
            }            
        } catch(Exception $e) {
            $message = $e->getMessage();            
        }    
        
        //Now doing db updates for the orders 
        if($transactionState == 'paymentSuccessfull')
        {
            $message = '---Payment Successful---'."\r\n";
            $message .= 'Order ID: ' . $this->session->data['order_id'] . "\r\n";
            $message .= 'PayU Reference: ' . $this->request->get['PayUReference'] . "\r\n";
            foreach ($response['soap_response']['paymentMethodsUsed'] as $key => $value) {
                $message .= ucwords($key) . ': ' . $value . "\r\n";
            }
            $this->model_checkout_order->update($this->session->data['order_id'], 2, $message, true);            
            $this->response->redirect($this->url->link('checkout/success', 'token=' . $this->session->data['token'], 'SSL'));            
        } else if($transactionState == "failure") {
            $this->data['heading_title'] = "Payment Failed";
            $this->data['notification_message'] = $message;
            $this->data['continue'] = $this->url->link('checkout/checkout');
            
            $message = "Payment failed. Reason: " . $message;
            
            $this->model_checkout_order->update($this->session->data['order_id'], 10, $message, false);
        }

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payu_easyplus_response.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payu_easyplus_response.tpl';
        } else {
            $this->template = 'default/template/payment/payu_easyplus_response.tpl';
        }

        $this->response->setOutput($this->render());		  
    }
    
    public function cancel() 
    {        
        $this->load->model('checkout/order');

        $this->data['heading_title'] = "Payment Cancelled";
        $this->data['notification_message'] = 'Payment cancelled on PayU payment page';
        $this->data['continue'] = $this->url->link('checkout/checkout');
        
        $message = $this->data['notification_message'];

        $this->model_checkout_order->update($this->session->data['order_id'], 7, $message, true);

        $this->children = array(
            'common/column_left',
            'common/column_right',
            'common/content_top',
            'common/content_bottom',
            'common/footer',
            'common/header'
        );

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/payu_easyplus_cancel.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/payu_easyplus_cancel.tpl';
        } else {
            $this->template = 'default/template/payment/payu_easyplus_cancel.tpl';
        }

        $this->response->setOutput($this->render());       
    }
	
    public function callback() {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($this->request->get));
    }

    public function ipn()
    {
        $ipnData = file_get_contents('php://input');
        $xml = @simplexml_load_string($ipnData);

        if(false === $xml)
            return;

        $ipn = $this->parseXMLToArray($xml);

        if(false === $ipn)
            return;

        if (!isset($ipn['MerchantReference'])) {
            return;
        }

        $orderid = $mb_data['MerchantReference'];
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($orderid);

        $payUReference = intval($ipn['PayUReference']);
        $txn_type = $ipn['TransactionType'];
        $payment_amount = (float)$ipn['PaymentMethodsUsed']['AmountInCents'] / 100;
        $payment_currency = $ipn['Basket']['CurrencyCode'];
        $payment_status = $ipn['TransactionState'];
        $hash = $ipn['IpnExtraInfo']['ResponseHash'];

        if($order_info) {
            $order_id = $order_info['order_id'];
            $ipnNote = '-----PAYU IPN RECIEVED---' . "\r\n";
            $ipnNote .= 'PayU Reference: ' . $payUReference . "\r\n";
            switch ($payment_status) {
              case 'SUCCESSFUL':
                if (abs($payment_amount - $order_info['total']) > 0.01) {
                    $ipnNote .= 'Payment did not equal the order total. ';
                }
                $this->model_checkout_order->update($order_id, 5, $ipnNote . 'PayU IPN reported a payment of ' . $payment_amount, true);
                break;

              case 'EXPIRED':
                $this->model_checkout_order->update($order_id, 14, $ipnNote . 'The reserve of funds has failed and cannot be finalized.');
                break;

              case 'FAILED':
                $this->model_checkout_order->update($order_id, 10, $ipnNote . "The customer's attempted payment failed.");
                break;

              case 'AWAITING_PAYMENT':
                $this->model_checkout_order->update($order_id, 1, $ipnNote . 'Awating Payment confirmation for EFT PRO at PayU: ' . $ipnData['resultMessage']);
                break;

              case 'PROCESSING':
                $this->model_checkout_order->update($order_id, 2, $ipnNote . 'A payment has been created but not finalized.');
                break;

              case 'TIMEOUT':
                $this->model_checkout_order->update($order_id, 10, $ipnNote . 'A payment has timed out during the processing state.');
                break;
            }
        } else {
            $this->model_checkout_order->update($order_id, 1, $ipnNote . 'PayU IPN transaction failed verification for this order.');
        }

        header("HTTP/1.1 200 Ok");
    }

    private function parseXMLToArray($xml) 
    {
        if($xml->count() <= 0)
            return false;

        $data = array();
        foreach ($xml as $element) {
            if($element->children()) {
                foreach ($element as $child) {
                    if($child->attributes()) {
                        foreach ($child->attributes() as $key => $value) {
                            $data[$element->getName()][$child->getName()][$key] = $value->__toString();
                        }
                    } else {
                        $data[$element->getName()][$child->getName()] = $child->__toString();
                    }
                }
            } else {
                $data[$element->getName()] = $element->__toString();
            }
        }
        return $data;
    }

    private function getRealClientIPAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip=$_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip=$_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    }
}
?>
