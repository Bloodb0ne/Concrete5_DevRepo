<?php   
namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultiuserPicker;

use Loader;
use user;
use Group;
use \Concrete\Core\Foundation\Object;

class Value extends Object {
	
	protected $atKeyTableName = "atMultiuserPickerOptions";
	protected $atValueTableName = "atMultiuserPickerValues";

	public static function getByID($avID) {
		return new Value($avID);
	}

	public function __construct($avID) {
		$db = Loader::db();
		$value = $db->GetRow("select avID,userIDs,groupIDs from `atMultiuserPickerValues` where avID = ?", array($avID));
		$this->setPropertiesFromArray($value);
	}

	public function __toString() {
		return $this->userIDs.":".$this->groupIDs;
	}

	public function getUsers(){
		$userIDs = explode(",",$this->userIDs);
		$users = array();

		foreach ($userIDs as $uID) {
			$user = User::getByUserID(intval($uID));
			if(!is_object($user) || !$user->getUserID()) continue;   
			$users[]=$user; 
		}
		return $users;
	}

	public function getGroups(){
		$groupIDs = explode(",",$this->groupIDs);
		$groups = array();

		foreach ($groupIDs as $gID) {
			$group = Group::getByID(intval($gID));
			if(!is_object($group) || !$group->getGroupID()) continue;   
			$groups[]=$group; 
		}

		return $groups;
	}
}

?>