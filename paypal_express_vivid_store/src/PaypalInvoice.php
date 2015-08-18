<?php 
namespace Concrete\Package\PaypalExpressVividStore\Src;
use Session;
use Loader;

class PaypalInvoice{
	public static function remove(){
		Session::set('cart-invoice',null);
	}
	public static function get(){
		$invoice = Session::get('cart-invoice');
		if(!$invoice){
			//Create a new invoice
			$db = Loader::db();
			$res = $db->insert('VividPaypalInvoice',[]);

			$id = $db->lastInsertId();
			if($res){
				Session::set('cart-invoice',$id);
				return $id;
			}
		
		}else{
			return $invoice;
		}

		return false;
	}
	public static function getOrderID($invoiceID){
		$db = Loader::db();
		$res = $db->fetchAssoc('SELECT invoiceID,oID FROM VividPaypalInvoice WHERE invoiceID = ?', array($invoiceID));
		if($res && is_array($res) && isset($res['oID'])){
			return $res['oID'];		
		}else{
			return false;
		}
	}
	public static function setOrderID($invoiceID,$orderID){
		$db = Loader::db();
		$res = $db->update('VividPaypalInvoice',['oID'=>$orderID],['invoiceID'=>$invoiceID]);
		return $res;

	}

	public function handleOrderEvent($order){
		$invoice = self::get();
		$oID = $order->getOrderID();
		self::setOrderID($invoice,$oID);
		self::remove();
	}
}

?>