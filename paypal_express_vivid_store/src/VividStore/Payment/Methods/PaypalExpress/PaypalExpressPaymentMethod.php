<?php 
namespace Concrete\Package\PaypalExpressVividStore\Src\VividStore\Payment\Methods\PaypalExpress;

use \Concrete\Package\VividStore\Src\VividStore\Payment\Method as PaymentMethod;
use \Concrete\Package\VividStore\Src\VividStore\Cart\Cart as VividCart;
use \Concrete\Package\VividStore\Src\VividStore\Product\Product as VividProduct;
use Concrete\Package\VividStore\Src\VividStore\Utilities\Price;
use Package;
use Core;
use Config;
use Loader;
use URL;
use Session;

use Concrete\Package\PaypalExpressVividStore\Src\PaypalInvoice as Invoice;

defined('C5_EXECUTE') or die(_("Access Denied."));
class PaypalExpressPaymentMethod extends PaymentMethod
{
    public function dashboardForm()
    {
        $pkg = Package::getByHandle('core_commerce');
        $this->set('paypalExpressMode', Config::get('vividstore.paypalExpressMode'));
        $this->set('paypalExpressTransactionType', Config::get('vividstore.paypalExpressTransactionType'));
        

        $this->set('paypalExpressLogoPath', Config::get('vividstore.paypalExpressLogoPath'));
        
        $cur_code = Config::get('vividstore.paypalExpressCurrencyCode');
        $this->set('paypalExpressCurrencyCode', (strlen($cur_code)?$cur_code:'USD'));

        //API Credentials
        $this->set('paypalExpressUsername', Config::get('vividstore.paypalExpressUsername'));
        $this->set('paypalExpressPassword', Config::get('vividstore.paypalExpressPassword'));
        $this->set('paypalExpressSignature', Config::get('vividstore.paypalExpressSignature'));

        $paypal_currency_codes = array(
            'AUD'=>t('Australian Dollar'),
            'CAD'=>t('Canadian Dollar'),
            'CZK'=>t('Czech Koruna'),
            'DKK'=>t('Danish Krone'),
            'EUR'=>t('Euro'),
            'HKD'=>t('Hong Kong Dollar'),
            'HUF'=>t('Hungarian Forint'),
            'ILS'=>t('Israeli New Sheqel'),
            'JPY'=>t('Japanese Yen'),
            'MXN'=>t('Mexican Peso'),
            'NOK'=>t('Norwegian Krone'),
            'NZD'=>t('New Zealand Dollar'),
            'PLN'=>t('Polish Zloty'),
            'GBP'=>t('Pound Sterling'),
            'SGD'=>t('Singapore Dollar'),
            'SEK'=>t('Swedish Krona'),
            'CHF'=>t('Swiss Franc'),
            'USD'=>t('U.S. Dollar')
        );
        asort($paypal_currency_codes);
        $this->set('paypal_currency_codes',$paypal_currency_codes); 
        $this->set('form',Core::make("helper/form"));
    }
    
    public function save($data)
    {
        Config::save('vividstore.paypalExpressMode',$data['paypalExpressMode']);
        Config::save('vividstore.paypalExpressTransactionType',$data['paypalExpressTransactionType']);
        Config::save('vividstore.paypalExpressLogoPath',$data['paypalExpressLogoPath']);
        Config::save('vividstore.paypalExpressCurrencyCode',$data['paypalExpressCurrencyCode']);

        Config::save('vividstore.paypalExpressUsername',$data['paypalExpressUsername']);
        Config::save('vividstore.paypalExpressPassword',$data['paypalExpressPassword']);
        Config::save('vividstore.paypalExpressSignature',$data['paypalExpressSignature']);
    }
    public function validate($args,$e)
    {
        
        //$e->add("error message");        
        return $e;
        
    }
    public function checkoutForm()
    {

    }
    
    public function submitPayment()
    {
        
        $crypt = Loader::helper('encryption');
        $paypal = new \Concrete\Package\PaypalExpressVividStore\Src\VividStore\Payment\Methods\PaypalExpress\Helpers\PaypalExpressHelper();

        $totals = VividCart::getTotals();
        

        if (Config::get('vividstore.paypalExpressMode') == 'test') { 
            $type = 'sandbox';
        } else {
            $type = 'live';
        }

        $invoice = Invoice::get();


        $conf = [
            'type' => $type,
            'username' => Config::get('vividstore.paypalExpressUsername'),
            'password' => Config::get('vividstore.paypalExpressPassword'),
            'signature'=> Config::get('vividstore.paypalExpressSignature'),

            'notify_url' => (string)URL::to('/paypal_express_vivid_store/notify'), //Action Path TODO can use external url i.e not in this class
            'cancel_url' =>  (string)URL::to('/paypal_express_vivid_store/cancel'), // We need the current checkout step to use the whole process TODO
            'return_url' =>  (string)URL::to('/paypal_express_vivid_store/return'), //On Complete return where >? TODO
            'cart_total' => $totals['total'],
            'cart_subtotal' => $totals['subTotal'],
            'cart_tax' => $totals['taxTotal'],
            'cart_shipping' => $totals['shippingTotal'],
            
            
            'invoice'=> $crypt->encrypt($invoice),
            'description' => t(SITE),
            'currency_code' => Config::get('vividstore.paypalExpressCurrencyCode'),
            'payment_action' => Config::get('vividstore.paypalExpressTransactionType')
        ];
        
        
        
        $paypal->setConfig($conf);
        
        if(isset($_GET['token']) && isset($_GET['PayerID']))
        {
            
            $token = urldecode($_GET['token']);
            $payer_id = urldecode($_GET['PayerID']);
            
            $data_get = $paypal->getExpressCheckout($token,$payer_id);
            $response = $paypal->makeRequest($data_get,$type);

            if($response['ACK'] == 'Success'){
                $data_do = $paypal->doExpressCheckout($token,$payer_id);
                $response = $paypal->makeRequest($data_do,$type);

                if($response['ACK'] == 'Success'){
                    return true;
                }
            }

        }else{
    
            $items = [];
           
            

            $cart = Session::get('cart');    
            if($cart){
                foreach ($cart as $cartItem){            
                    $pID = $cartItem['product']['pID'];
                    $qty = $cartItem['product']['qty'];
                    $product = VividProduct::getByID($pID);
                    if(is_object($product)){
                        $tempItem  = [];
                        $tempItem['name'] = $product->getProductName();
                        $tempItem['desc'] = strip_tags($product->getProductDesc());
                        $tempItem['price'] = $product->getFormattedPrice();
                        $tempItem['qty'] = $qty;
                        $items[] = $tempItem;
                    }
                }
            }
                
            $configData = [];
            $configData['items'] = $items;
            $configData['item_sum'] = $totals['total'];

            $data = $paypal->setExpressCheckout($configData);
            
            $response = $paypal->makeRequest($data,$type);
            
            if($response['ACK'] == 'Success'){
                //Redirect to paypal payment page
                header('Location: '.$paypal->getPaypalUrl($response['TOKEN']));
                exit();
            }else{
                
                return ['error'=>1,'errorMessage'=>print_r($response,true)];
            }
        }
       
        
    }

    
}

return __NAMESPACE__;