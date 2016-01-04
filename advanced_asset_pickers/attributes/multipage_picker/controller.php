<?php   

namespace Concrete\Package\AdvancedAssetPickers\Attribute\MultipagePicker;
use Loader;
use View; 

use Concrete\Package\AdvancedAssetPickers\Attribute\MultipagePicker\Value as MultipagePickerAttributeTypeValue;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Attribute\Controller  {

	protected $atKeyTableName = "atMultipagePickerOptions";
	protected $atValueTableName = "atMultipagePickerValues";

	protected $searchIndexFieldDefinition = array(
    	'name'=>'pageIDs',
        'type' => 'string',
        'options' => array('default' => null, 'notnull' => false)
	);

	public function getSearchIndexValue() {
	    $v = $this->getValue();
	    $args = array();
	    $args['pageIDs'] = $v->pageIDs;
	    return $args;
	}
	public function searchKeywords($keywords) {
		$db = Loader::db();
    	$qkeywords = $db->quote('%' . $keywords . '%');
    	$str = '(ak_' . $this->attributeKey->getAttributeKeyHandle() . '_pageIDs like '.$qkeywords.')';
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

		$fields = array('single','path','typeHandle','parentId','preserveOrder','uniqueValues');
		$row = $db->GetRow('select '.implode(",",$fields).' from '.$this->atKeyTableName.' where akID = ?', array($ak->getAttributeKeyID()));

		//Fetches Options
		foreach ($fields as $field) {
			if($field == 'path' && $row['path'] == 0){
				$row[$field] = "";
			}
			$this->set($field,$row[$field]);
		}

		$this->set("akHandle",$ak->getAttributeKeyHandle());
	}

	public function getSelectedOptions() {
		$db = Loader::db();
		$avID = $this->getAttributeValueID();
		$value = $db->GetRow("select avID,pageIDs from `".$this->atValueTableName."` where avID = ?", array($avID));
		
		//Fetches files from ids into $files
		$pageIDs = explode(",",$value['pageIDs']);
		$pages = array();

		foreach ($pageIDs as $fID) {
			$page = Page::getByID(intval($fID));
			if(!is_object($page) || !$page->getCollectionID()) continue;   
			$pages[]=$page; 
		}

		return $pages;
	}
	

	public function getValue() {
		return new MultipagePickerAttributeTypeValue($this->getAttributeValueID());
	}

	public function form(){
		$this->load();
		$this->requireAsset('css', 'asset_picker_style');
		$this->requireAsset('javascript', 'page_selector');
		$this->set("avID",$this->getAttributeValueID());
	}

	public function type_form(){
		$this->load();
		$this->requireAsset('css', 'asset_picker_style');
		$this->requireAsset('core/sitemap');
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
	    
	    if(isset($data['pageIDs'])){

	    	if($this->get("uniqueValues") == 1){
		    	$data['pageIDs'] = array_unique($data['pageIDs']);
		    	if($this->get("preserveOrder") == 1){
					ksort($data['pageIDs']);
		    	}
		    }

	    	$pids = implode(",",$data['pageIDs']);
	   	
	   		$db->Replace($this->atValueTableName, array('avID' => $this->getAttributeValueID(),
	    	    'pageIDs' => $pids),
				'avID', true);
	    }
	    
	}
	public function saveKey($data) {
		
		$fields = array('single','path','typeHandle','parentId','preserveOrder','uniqueValues');
		
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

