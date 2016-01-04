<?php    

namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultifilePicker;
use Loader;
use View;
use Concrete\Package\AdvancedAssetPickers\Attribute\MultifilePicker\Value as MultifilePickerAttributeTypeValue;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Attribute\Controller  {

	protected $atKeyTableName = "atMultifilePickerOptions";
	protected $atValueTableName = "atMultifilePickerValues";

	protected $searchIndexFieldDefinition = array(
		'name'=>'fileIDs',
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)
    );

	public function getSearchIndexValue() {
	    $v = $this->getValue();
	    $args = array();
	    $args['fileIDs'] = $v->fileIDs;
	    return $args;
	}
	public function searchKeywords($keywords) {
		$db = Loader::db();
    	$qkeywords = $db->quote('%' . $keywords . '%');
    	$str = '(ak_' . $this->attributeKey->getAttributeKeyHandle() . '_fileIDs like '.$qkeywords.')';
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

		$fields = array('preserveOrder','uniqueValues','single');
		$row = $db->GetRow('select '.implode(",",$fields).' from '.$this->atKeyTableName.' where akID = ?', array($ak->getAttributeKeyID()));

		//Fetches Options
		foreach ($fields as $field) {
			$this->set($field,$row[$field]);
		}

		$this->set("akHandle",$ak->getAttributeKeyHandle());
	}

	public function getSelectedOptions(){
		$db = Loader::db();
		$avID = $this->getAttributeValueID();
		$value = $db->GetRow("select avID,fileIDs from `".$this->atValueTableName."` where avID = ?", array($avID));
		$fileIDs = explode(",",$value['fileIDs']);
		$files = array();

		foreach ($fileIDs as $fID) {
			$file = File::getByID(intval($fID));
			if(!is_object($file) || !$file->getFileID()) continue;   
			$files[]=$file; 
		}

		return $files;
	}
	public function getValue() {
		return new MultifilePickerAttributeTypeValue($this->getAttributeValueID());
	}

	public function getDisplayValue(){
		return $this->getValue();
	}
	
	public function form(){
		$this->load();
		$this->requireAsset('core/file-manager');
		
        $this->requireAsset('css', 'asset_picker_style');
		$this->requireAsset('javascript','file_selector');
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

		if(isset($data['fileIDs'])){

		    if($this->get("uniqueValues") == 1){
		    	$data['fileIDs'] = array_unique($data['fileIDs']);
		    	if($this->get("preserveOrder") == 1){
					ksort($data['fileIDs']);
		    	}
		    }
				    
	    	$pids = implode(",",$data['fileIDs']);
		    $db->Replace($this->atValueTableName, array('avID' => $this->getAttributeValueID(),
		        'fileIDs' => $pids
		        ),
		        'avID', true
		    );
	    }
	    
	}
	public function saveKey($data) {
		
		$fields = array('preserveOrder','uniqueValues','single');
		
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

