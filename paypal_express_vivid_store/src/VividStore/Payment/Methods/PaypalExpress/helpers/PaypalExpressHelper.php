<?php 
namespace Concrete\Package\PaypalExpressVividStore\Src\VividStore\Payment\Methods\PaypalExpress\Helpers;
class PaypalExpressHelper{

	private $config = [
		"version"	=> "119"
	];

	private $urls = [
		'sandbox'	=> "https://www.sandbox.paypal.com/cgi-bin/webscr",
		'live'		=> "https://www.paypal.com/cgi-bin/webscr"
	];

	private $nvp_url = [
		'sandbox'	=> "https://api-3t.sandbox.paypal.com/nvp",
		'live'		=> "https://api-3t.paypal.com/nvp"
	];

	private function parseResponseString($resp_string = ""){
		$response_values = [];

        $elements = explode("&",$resp_string);
        foreach ($elements as $value) {
        	$data_value = explode("=",$value);
        	$response_values[$data_value[0]] = urldecode($data_value[1]);
        }

        return $response_values;
	}

	public function setConfig($data){
		// $this->config['notify_url'] = $data['notify_url'];
		foreach ($data as $key => $value) {
			//Validate this shite ?
			$this->config[$key] = $value;
		}
	}
	
	public function validateIPN(){

		header("HTTP/1.1 200 OK");

		$data = $_POST;
		$data['cmd'] = urlencode('_notify-validate');
		

		$response = $this->makeRequest($data,$this->config['type'],'normal',false);
		$response = strtolower($response);
		
		if($response == "verified"){
			return true;
		}elseif($response == "invalid"){
			return 	false;
		}

	}

	public function doExpressCheckout($token,$payer_id){
		//Basic fields

        $fields["METHOD"]	= "DoExpressCheckoutPayment";
		$fields["USER"]		= $this->config['username'];
		$fields["PWD"]		= $this->config['password'];
		$fields['SIGNATURE']= $this->config['signature'];
		$fields["VERSION"]	= $this->config['version'];
		$fields["TOKEN"]	= $token;
		$fields["PAYERID"]	= $payer_id;

		$fields["PAYMENTREQUEST_0_AMT"] = $this->config['cart_total'];
		$fields["PAYMENTREQUEST_0_PAYMENTACTION"] = $this->config['payment_action'];
		$fields["PAYMENTREQUEST_0_CURRENCYCODE"]	= $this->config['currency_code'];

		$fields["PAYMENTREQUEST_0_NOTIFYURL"] = $this->config['notify_url'];
		return $fields;
	}

	public function getExpressCheckout($token,$payer_id){
		//Basic fields

        $fields["METHOD"]	= "GetExpressCheckoutDetails";
		$fields["USER"]		= $this->config['username'];
		$fields["PWD"]		= $this->config['password'];
		$fields['SIGNATURE']= $this->config['signature'];
		$fields["VERSION"]	= $this->config['version'];
		$fields["TOKEN"]	= $token;

		return $fields;
	}
	public function setExpressCheckout($user_data = []){
		//Basic fields

		$fields["METHOD"]	= "SetExpressCheckout";
		$fields["USER"]		= $this->config['username'];
		$fields["PWD"]		= $this->config['password'];
		$fields['SIGNATURE']= $this->config['signature'];
		$fields["VERSION"]	= $this->config['version'];


		// Cart Products Options
		$fields["PAYMENTREQUEST_0_ITEMAMT"]			= $this->config['cart_subtotal'];
        $fields["PAYMENTREQUEST_0_TAXAMT"]			= $this->config['cart_tax'];
        $fields["PAYMENTREQUEST_0_SHIPPINGAMT"]		= $this->config['cart_shipping'];
        $fields["PAYMENTREQUEST_0_DESC"]			= $this->config['description'];
        $fields["PAYMENTREQUEST_0_INVNUM"]			= $this->config['invoice'];

        $fields["PAYMENTREQUEST_0_AMT"]				= $this->config['cart_total'];
        $fields["PAYMENTREQUEST_0_CURRENCYCODE"]	= $this->config['currency_code'];
        $fields["PAYMENTREQUEST_0_PAYMENTACTION"]	= $this->config['payment_action'];

        //Cart Elements 
        
        foreach($user_data['items'] as $key => $item)
        {
            $fields["L_PAYMENTREQUEST_0_NAME"		. $key]		= $item['name'];
            // $fields["L_PAYMENTREQUEST_0_NUMBER"		. $key]		= $item['num'];
            $fields["L_PAYMENTREQUEST_0_DESC"		. $key]		= $item['desc'];
            $fields["L_PAYMENTREQUEST_0_AMT"		. $key]		= $item['price'];
            $fields["L_PAYMENTREQUEST_0_QTY"		. $key]		= $item['qty'];

        }
		
		//Shipping Options
		$fields["REQCONFIRMSHIPPING"] = 0;
		$fields["ADDROVERRIDE"] = 0;
		$fields["NOSHIPPING"] = 1;

		//URL Options
		$fields["RETURNURL"]	= $this->config['return_url'];
       	$fields["CANCELURL"]	= $this->config['cancel_url'];
		//Encode everything with urlencode()

		return $fields;
	}

	public function getPaypalUrl($token){
		$type = $this->config['type'];
		return $this->urls[$type]."?cmd=_express-checkout&token=".$token;
	}

	
	public function makeRequest($fields,$type = 'sandbox',$api = 'nvp',$parse = true){

		$post_string = "";
	    $post_string = http_build_query($fields);

	    if($api == 'nvp'){
			$request = curl_init($this->nvp_url[$type]); // initiate curl object
	    }elseif($api == 'normal'){
	    	$request = curl_init($this->urls[$type]); // initiate curl object
	    }
		
        curl_setopt($request, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); // Returns response data instead of TRUE(1)
        curl_setopt($request, CURLOPT_POSTFIELDS, $post_string); // use HTTP POST to send form data
        curl_setopt($request, CURLOPT_SSL_VERIFYPEER, FALSE); // uncomment this line if you get no gateway response.
        $post_response = curl_exec($request); // execute curl post and store results in $post_response

        if(curl_errno($request)){
			return false;
		}

        curl_close ($request); // close curl object
        if($parse){
			return $this->parseResponseString($post_response);
        }else{
        	return $post_response;
        }
        
	} 
}

?>