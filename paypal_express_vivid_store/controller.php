<?php    

namespace Concrete\Package\PaypalExpressVividStore;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Theme\Theme;
use Route;
use AssetList;
use Loader;
use Events;
use \Concrete\Package\VividStore\Src\VividStore\Payment\Method as PaymentMethod;

use Concrete\Package\PaypalExpressVividStore\Src\PaypalInvoice as Invoice;
use Concrete\Package\VividStore\Src\VividStore\Orders\OrderStatus\OrderStatus;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'paypal_express_vivid_store';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';
	public function on_start() {
		Route::register('/paypal_express_vivid_store/return','Concrete\Package\PaypalExpressVividStore\Src\PaypalHandler::paypal_return');

		Route::register('/paypal_express_vivid_store/notify','Concrete\Package\PaypalExpressVividStore\Src\PaypalHandler::paypal_notify');

		Route::register('/paypal_express_vivid_store/cancel','Concrete\Package\PaypalExpressVividStore\Src\PaypalHandler::paypal_cancel');

		Events::addListener('on_vividstore_order', function($event){

            $order = $event->getCurrentOrder();
            //TODO: maybe try to get the payment method handle
            if($order->getPaymentMethodName() == 'Paypal Express'){
	            $invoice = Invoice::get();
				$oID = $order->getOrderID();
				Invoice::setOrderID($invoice,$oID);
				Invoice::remove();
            }
        });
	}
	public function getPackageDescription() {
		return t("Adds a Paypal Express Method for VividStore");
	}
	
	public function getPackageName() {
		return t("Paypal Express for VividStore");
	}
	
	public function install() {
		$pkg = parent::install();
		OrderStatus::add('canceled','Canceled');
		PaymentMethod::add('paypal_express','Paypal Express',$pkg);
	}

	public function uninstall() {

		$paypal = PaymentMethod::getByHandle('paypal_express');
        if(is_object($paypal)){
            $paypal->delete();
        }

        parent::uninstall();
	}
}