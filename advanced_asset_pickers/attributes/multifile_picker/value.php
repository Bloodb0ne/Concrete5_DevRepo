<?php   
namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultifilePicker;

use Loader;
use File;
use \Concrete\Core\Foundation\Object;

defined('C5_EXECUTE') or die("Access Denied.");

class Value extends Object  {
	
	protected $atKeyTableName = "atMultifilePickerOptions";
	protected $atValueTableName = "atMultifilePickerValues";


	public static function getByID($avID) {
		
		$mpatv = new Value($avID);

		return $mpatv;
	}

	public function __construct($avID) {
		$this->avID = $avID;
		$db = Loader::db();
		$value = $db->GetRow("select avID,fileIDs from `atMultifilePickerValues` where avID = ?", array($avID));
		$this->setPropertiesFromArray($value);
	}

	public function __toString() {
		return $this->fileIDs;
	}
	public function getFiles() {
		$fileIDs = explode(",",$this->fileIDs);
		$files = array();

		foreach ($fileIDs as $fID) {
			$file = File::getByID(intval($fID));
			if(!is_object($file) || !$file->getFileID()) continue;   
			$files[]=$file; 
		}
		return $files;
	}
}

?>