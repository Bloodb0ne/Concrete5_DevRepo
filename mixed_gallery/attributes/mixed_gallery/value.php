<?php  
namespace Concrete\Package\MixedGallery\Attribute\MixedGallery;

use Loader;
use File;
use \Concrete\Core\Foundation\Object;

defined('C5_EXECUTE') or die("Access Denied.");

class Value extends Object  {
	
	public static function getByID($avID) {
		$val = new Value($avID);
		return $val;
	}

	public function __construct($avID) {
		$this->avID = $avID;
		$db = Loader::db();
		$value = $db->GetRow("select avID,contentArray from `atMixedGallery` where avID = ?", array($avID));
		$this->setPropertiesFromArray($value);
	}

	public function __toString() {
		$items = json_decode($this->contentArray);
		if(!is_array($items)) return "0 Items";
		return count($items).' Items';

		// return $this->fileIDs;
	}
	public function getItems() {
		$items = json_decode($this->contentArray);
		if(!is_array($items)) return [];
		return array_map(function($item){
			if(is_numeric($item)){
				return File::getByID(intval($item));
			}else{
				return urldecode($item);
			}

		},$items);
	}
}