<?php 
namespace Concrete\Package\SimpleNewsletter\Src\Newsletter;

use Concrete\Core\Foundation\Object as Object;
use Database;
use File;
use User;
use UserInfo;
use Core;
use Package;
use Concrete\Core\Mail\Service as MailService;
use Session;
use Group;
use Events;
use Config;
use Loader;
use Concrete\Core\Utility\IPAddress;
use View;


defined('C5_EXECUTE') or die(_("Access Denied."));
class Email extends Object
{
	public static function getByID($eID) {
        $db = Database::get();
        $data = $db->GetRow("SELECT * FROM pkgNewsletterEmails WHERE eID=?",$eID);
        if(!empty($data)){
            $order = new Email();
            $order->setPropertiesFromArray($data);
        }
        return($order instanceof Email) ? $order : false;
    } 

    public static function getByEmail($email) {
        $db = Database::get();
        $data = $db->GetRow("SELECT * FROM pkgNewsletterEmails WHERE email=?",$email);
        if(!empty($data)){
            $order = new Email();
            $order->setPropertiesFromArray($data);
        }
        return($order instanceof Email) ? $order : false;
    } 


    public function add($data){
    	$db = Database::get();

    	$created = date("Y-m-d H:i:s");
		$confirmation = md5($created . $data['email']);
		$created = date("Y-m-d H:i:s");
		//Autoconfirm
		$confirmed = date("Y-m-d H:i:s");

		$keys = array('email','ip','confirmation','confirmed','created','unsubscribed');
        $vals = array($data['email'],$data['ip'],$confirmation,$confirmed,$created,0);
        $fields = implode(',',$keys);
        $placeholders = implode(',',array_map(function(){ return "?"; },$keys));

        $db->Execute("INSERT INTO pkgNewsletterEmails({$fields}) VALUES ({$placeholders})", $vals);
        $oID = $db->lastInsertId();
    }	
    public function save(){
    	$db = Database::get();

		$keys = array('confirmed','unsubscribed');
		$obj = $this;
        $vals = array_map(function($key) use($obj){
            return $obj->$key;
        },$keys);
        $vals[] = $this->eID;
        $fields = implode('=?,',$keys);

        $db->Execute("UPDATE pkgNewsletterEmails SET {$fields}=? WHERE eID = ?", $vals);
        $oID = $db->lastInsertId();
    }

    public function delete(){
    	if($this->eID){
	    	$db = Database::get();
	        $data = $db->Execute("DELETE FROM pkgNewsletterEmails WHERE eID=?",$this->eID);
    	}
    }

    public static function validate($data){
		$error = Loader::helper('validation/error');
		$email = $data['email'];

		if(!$email){
			$error->add(t("You must enter an email."));
		}
		if(!self::valid_email($email)){
			$error->add(t("Invalid email."));
		}
		if(!self::unique_email($email)){
			$error->add(t("This email is already signed up."));
		}
		// Check Unique
		return $error;
	}

	public static function valid_email($email){
		$regex = "/^\S+@\S+\.\S+$/";
		return (bool)preg_match($regex, $email);
	}

	public static function unique_email($email){
		$db = Loader::db();
		$prep_query = "SELECT COUNT(eID) FROM pkgNewsletterEmails WHERE email = ?";
		$count = $db->GetOne($prep_query,[$email]);
		return $count == 0;
	}

	public static function isSubscribed($email){
		$db = Loader::db();
		$prep_query = "SELECT COUNT(eID) FROM pkgNewsletterEmails WHERE email = ? AND unsubscribed = 0";
		$count = $db->GetOne($prep_query,[$email]);
		return $count == 1;
	}

    public function getEmail(){
    	return $this->email;
    }

    public function getID(){
    	return $this->eID;
    }

    public function getIP(){
    	$ip = new IPAddress($this->ip,true);
    	return $ip->getIp(IPAddress::FORMAT_IP_STRING);
    }

    public function getDateCreated(){
    	return $this->created;
    }

    public function getDateConfirmed(){
    	return $this->confirmed;
    }

    public function getUnsubscribed(){
    	return $this->unsubscribed;
    }


    public function generateConfirmationUrl(){
    	$email = urlencode($this->email);
		return View::url("/confirm_newsletter_signup/").
				"?e={$email}&".
				"h={$this->confirmation}";
	}
	public function generateUnsubscribeUrl(){
		$email = urlencode($this->email);
		return View::url("/newsletter_signup/unsubscribe/").
				"?e={$email}&".
				"h={$this->confirmation}";
	}
}

?>