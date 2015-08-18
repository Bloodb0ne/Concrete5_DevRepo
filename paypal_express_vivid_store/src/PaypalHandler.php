<?php 
namespace Concrete\Package\PaypalExpressVividStore\Src;
use Controller;
use URL;

use Concrete\Package\VividStore\Controller\SinglePage\Checkout;
use Concrete\Package\VividStore\Src\VividStore\Orders\Order;
use \Concrete\Package\VividStore\Src\VividStore\Orders\OrderStatus\History as OrderHistory;
use \Concrete\Package\VividStore\Src\VividStore\Orders\OrderStatus\OrderStatus;

class PaypalHandler extends Controller{

	public function paypal_notify(){
		$crypt = Loader::helper('encryption');
        $paypal = new \Concrete\Package\PaypalExpressVividStore\Src\VividStore\Payment\Methods\PaypalExpress\Helpers\PaypalExpressHelper();

        if (Config::get('vividstore.paypalExpressMode') == 'test') { 
            $type = 'sandbox';
        } else {
            $type = 'live';
        }

		$conf = [
			'type' => $type
		];

		$paypal->setConfig($conf);

		if($paypal->validateIPN()){

			if(Config::get('vividstore.paypalExpressTransactionType') == "Authorization"){
				$status_message = strtolower($_REQUEST['auth_status']);
			}else{
				$status_message = strtolower($_REQUEST['payment_status']);
			}

			if($status_message == 'completed'){
				$status = OrderStatus::getByHandle('complete');;
			}elseif($status_message == 'denied' || $status_message == 'reversed'){
				$status = OrderStatus::getByHandle('complete');
			}

			$crypt = Loader::helper('encryption');
			$invoice = $crypt->decrypt($_REQUEST['invoice']);
			$oID = Invoice::getOrderID($invoice);

			$order = Order::getById($oID);
			if($order){
				$order->updateStatus($status);
			}
		}
	}

	public function paypal_return(){
		$_POST['payment-method'] = 'paypal_express';
		Checkout::submit();
		
		// $url = URL::to('/checkout/submit');
		// $data = http_build_query($_GET, '', '&amp;');
		// // echo($url.'?'.$data);

		// header('Location: '.$url.'?'.$data);
		// exit();
	}

	public function paypal_cancel(){
		$url = (string)URL::to('/checkout');
		header('Location: '.$url);
		exit();
	}


}
?>