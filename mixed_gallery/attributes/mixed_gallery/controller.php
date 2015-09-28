<?php  

namespace Concrete\Package\MixedGallery\Attribute\MixedGallery;
use Loader;
use View;
use \Concrete\Core\Attribute\Controller as AttributeController;
use Concrete\Package\MixedGallery\Attribute\MixedGallery\Value as MixedGalleryAttributeTypeValue;

class Controller extends AttributeController{

	protected $atValueTableName = "atMixedGallery";


	public function form(){
		$v = View::getInstance();
        $v->requireAsset('core/file-manager');

        $this->set('items',$this->getRealValues());
	}

	public function type_form(){
		//No advanced options added
	}


	public function load(){

		$ak = $this->getAttributeKey();
		
		if (!is_object($ak)) {
			return false;
		}

		$this->set("akHandle",$ak->getAttributeKeyHandle());
	}

	public function saveKey(){

	}

	public function saveForm($data) {
		$this->saveValue($data);
	}

	public function validateForm($data) {
		return true;
	}

	public function getRealValues(){
		return $this->getValue()->getItems();
	}
	public function getValue() {
		return MixedGalleryAttributeTypeValue::getByID($this->getAttributeValueID());
	}

	public function saveValue($data){
		$db = Loader::db();

	   	// if(is_null($data)) return false;
	    // extract($data);

	    $this->load();
	    $ak = $this->getAttributeKey();

	  	if(!isset($data['galleryItems'])){
	  		$data['galleryItems'] = [];
	  	}
	    
	   
    	$items = array_map(function($item){
	    	if(is_numeric($item)){
	    		return intval($item);
	    	}
	    	return urlencode($item);
	    },$data['galleryItems']);

    	$content = json_encode($items);



	    $db->Replace($this->atValueTableName, array('avID' => $this->getAttributeValueID(),
	        'contentArray' => $content
	        ),
	        'avID', true
	    );
	    
	}

	public function deleteValue(){
        $db = Loader::db();
        $db->Execute("delete from {$this->atValueTableName} where avID = ?", array($this->getAttributeValueID()));
	}

	public function deleteKey(){
		$db = Loader::db();
        $arr = $this->attributeKey->getAttributeValueIDList();
        foreach ($arr as $id) {
            $db->Execute("delete from {$this->atValueTableName} where avID = ?", array($id));
        }
	}

	
}