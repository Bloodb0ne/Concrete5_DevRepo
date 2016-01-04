<?php   
namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultipagePicker;
use Loader;
use Page;
use \Concrete\Core\Foundation\Object;

defined('C5_EXECUTE') or die("Access Denied.");

class Value extends Object {
	
	protected static $atKeyTableName = "atMultipagePickerOptions";
	protected static $atValueTableName = "atMultipagePickerValues";

	public static function getByID($avID) {
		return new Value($avID);
	}

	public static function getOptions($akID){
		$db = Loader::db();

		$fields = array('single','path','typeHandle','parentId');
		$row = $db->GetRow('select '.implode(",",$fields).' from '.self::$atKeyTableName.' where akID = ?', array($akID));
		return $row;
	}

	public function __construct($avID) {
		$db = Loader::db();
		$value = $db->GetRow("select avID,pageIDs from `atMultipagePickerValues` where avID = ?", array($avID));
		$this->setPropertiesFromArray($value);
	}

	public function __toString() {
		return $this->pageIDs;
	}

	public function getPages(){
		$pageIDs = explode(",",$this->pageIDs);
		$pages = array();

		foreach ($pageIDs as $fID) {
			$page = Page::getByID(intval($fID));
			if(!is_object($page) || !$page->getCollectionID()) continue;   
			$pages[]=$page; 
		}
		return $pages;
	}
}

?>