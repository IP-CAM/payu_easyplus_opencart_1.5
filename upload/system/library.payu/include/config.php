<?php


class PayUConfig {
    
    public static function getConfig() {
        
        $payUConfig = array();
        
        //Redirect payment method
        $payUConfig['payu_easyplus']['safe_key']  = '{CE62CE80-0EFD-4035-87C1-8824C5C46E7F}';        
        $payUConfig['payu_easyplus']['api_username'] = '100032';
        $payUConfig['payu_easyplus']['api_password'] = 'PypWWegU';
        $payUConfig['payment_title'] = 'PayU Secure Payments (Credit/Debit cards, eBucks et al)';

        //SafeShop Pro Details
        $payUConfig['payu_pro']['safe_key']  = '{E7831EC1-AA49-4ADB-893E-408C3683B633}';          

        $payUConfig['supported_currencies'] = array(
            array('value' => 'ZAR', 'name' => 'South African Rand'), 
            array('value' => 'NGN', 'name' => 'Nigerian Naira'),
        );
        $payUConfig['supported_payment_methods'] = array(
            array('value' => 'LOYALTY', 'name' => 'Loyalty'),
            array('value' => 'CREDITCARD', 'name' => 'Credit card'),
            array('value' => 'CREDITCARD_PAYU', 'name' => 'PayU Credit card'),
            array('value' => 'WALLET', 'name' => 'eWallet'),
            array('value' => 'DISCOVERYMILES', 'name' => 'Discovery miles'),
            array('value' => 'GLOBALPAY', 'name' => 'Global pay'),
            array('value' => 'DEBITCARD', 'name' => 'Debit card'),
            array('value' => 'EBUCKS', 'name' => 'eBucks'),
            array('value' => 'EFT', 'name' => 'EFT'),
            array('value' => 'MASTERPASS', 'name' => 'MasterPass Digital Wallet'),
            array('value' => 'RCS', 'name' => 'RCS'),
            array('value' => 'EFT_PRO', 'name' => 'EFT Pro'),
            array('value' => 'WALLET_PAYU', 'name' => 'PayU eWallet'),
        );         
        
        return $payUConfig;        
    }
}