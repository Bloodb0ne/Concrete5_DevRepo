<?php    

namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultiuserPicker;
use Loader;
use View;
use Concrete\Package\AdvancedAssetPickers\Attribute\MultiuserPicker\Value as MultiuserPickerAttributeTypeValue;
defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Attribute\Controller  {

	protected $atKeyTableName = "atMultiuserPickerOptions";
	protected $atValueTableName = "atMultiuserPickerValues";

	protected $searchIndexFieldDefinition = array(
		['name'=>'fileIDs',
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)],
        ['name'=>'groupIDs',
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)]
    );
	public function getSearchIndexValue() {
	    $v = $this->getValue();
	    $args = array();
	    $args['userIDs'] = $v->userIDs;
	    $args['groupIDs'] = $v->groupIDs;
	    return $args;
	}
	public function searchKeywords($keywords) {
		$db = Loader::db();
    	$qkeywords = $db->quote('%' . $keywords . '%');
    	$str = '(ak_' . $this->attributeKey->getAttributeKeyHandle() . '_userIDs like '.$qkeywords.' or ';
    	$str .= 'ak_' . $this->attributeKey->getAttributeKeyHandle() . '_groupIDs like '.$qkeywords.')';
    	return $str;
	}
	public function load(){
		/*
			Loads the data
		*/
		$ak = $this->getAttributeKey();
		if (!is_object($ak)) {
			return false;
		}
		
		$db = Loader::db();

		$fields = array('single','preserveOrder','uniqueValues');
		$row = $db->GetRow('select '.implode(",",$fields).' from '.$this->atKeyTableName.' where akID = ?', array($ak->getAttributeKeyID()));

		//Fetches Options
		foreach ($fields as $field) {
			$this->set($field,$row[$field]);
		}

		$this->set("akHandle",$ak->getAttributeKeyHandle());
	}

	
	public static function getSelectedOptions() {
		$db = Loader::db();
		$value = $db->GetRow("select avID,userIDs from `atMultiuserPickerValues` where avID = ?", array($avID));
		
		$userIDs = explode(",",$value['userIDs']);
		$users = array();

		foreach ($userIDs as $fID) {
			$user = User::getByUserID(intval($fID));
			if(!is_object($user) || !$user->getUserID()) continue;   
			$users[]=$user; 
		}
		
		return $users;
	}
	public function getValue() {
		return new MultiuserPickerAttributeTypeValue($this->getAttributeValueID());
	}
	public function form(){
		$this->load();
		// $this->requireAsset('css', 'core/file-manager');
		$this->requireAsset('css', 'asset_picker_style');
		$this->requireAsset('javascript','user_selector');
		$this->requireAsset('javascript','group_selector');
		$this->set("avID",$this->getAttributeValueID());
		
	}

	public function type_form(){
		$this->load();
		
	}

	public function saveForm($data) {
		$this->saveValue($data);
	}

	public function validateForm($data) {
		return true;
	}

	public function saveValue($data) {
		$db = Loader::db();
		
	    if(is_null($data)) return false;
	    extract($data);

	    $this->load();
	    $ak = $this->getAttributeKey();

	    if(!isset($data['userIDs'])){
	    	$data['userIDs'] = array();
	    }

	    if(!isset($data['groupIDs'])){
	    	$data['groupIDs'] = array();
	    }

	    //Remove Duplicates
	    if($this->get("uniqueValues") == 1){
	    	$data['userIDs'] = array_unique($data['userIDs']);
	    	if($this->get("preserveOrder") == 1){
				ksort($data['userIDs']);
	    	}
	    }

	    if($this->get("uniqueValues") == 1){
	    	$data['groupIDs'] = array_unique($data['groupIDs']);
	    	if($this->get("preserveOrder") == 1){
				ksort($data['groupIDs']);
	    	}
	    }
	    
	    $uids = implode(",",$data['userIDs']);
	    $gids = implode(",",$data['groupIDs']);

	    $db->Replace($this->atValueTableName, array('avID' => $this->getAttributeValueID(),
	        'userIDs' => $uids,
	        'groupIDs' => $gids
	        ),
	        'avID', true
	    );
	}
	public function saveKey($data) {
		
		$fields = array('single','preserveOrder','uniqueValues');
		
		$ak = $this->getAttributeKey();
		$repVals = array('akID' => $ak->getAttributeKeyID());

		foreach ($fields as $field) {
			$$field = $data[$field];
			if (!$$field) {
				$$field = 0;
			}
			$repVals[$field] = $$field;
		}
		
		$db = Loader::db();
		
		$ak = $this->getAttributeKey();
		$db->Replace(
					$this->atKeyTableName,
					$repVals,
					array('akID'),
					true);

	}

	public function deleteKey() {
	    $db = Loader::db();
	    $arr = $this->attributeKey->getAttributeValueIDList();
	    $akID = $this->getAttributeKey()->getAttributeKeyID();

	    foreach($arr as $id) {
	        $db->Execute('delete from '.$this->atValueTableName.' where avID = ?', array($id));
	    }
	    $db->Execute('delete from '.$this->atKeyTableName.' where akID = ?', array($akID));
	}
	public function deleteValue() {
	    $db = Loader::db();
	    $id = $this->getAttributeValueID();
	    $db->Execute('delete from '.$this->atValueTableName.' where avID = ?', array($id));
	}
}

