<?php  
namespace Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog;

use Controller;
use GroupList;
use \Concrete\Core\User\Group\GroupSearchColumnSet;
use \Concrete\Core\Search\Result\Result as SearchResult;
use Permissions;
use Loader;
use stdClass;
use TaskPermission;
use URL;
use \Concrete\Core\Search\Column\Column;

class Groups extends Controller {

	protected $fields = array();
	protected $multipleSelection;

	public function __construct() {
		$this->groupList = new GroupList();
		$this->multipleSelection = $_REQUEST['multipleSelection'];
	}

	public function getSelectionMode(){
        if($this->multipleSelection){
            return 'choose';
        }else{
            return 'chooseall';
        }
    }

	public function search() {
		$tp = new TaskPermission();
		if (!$tp->canAccessGroupSearch()) {
			return false;
		}

		if ($_REQUEST['filter'] == 'assign') {
			$this->groupList->filterByAssignable();
		} else {
			$this->groupList->includeAllGroups();
		}

		if (isset($_REQUEST['keywords'])) {
			$this->groupList->filterByKeywords($_REQUEST['keywords']);
		}
		
		$this->groupList->sortBy('gID', 'asc');

		$columns = new GroupSearchColumnSet();

		$handleName = "filter".Loader::helper('text')->camelcase($this->attributeHandle).'Group';
        if(method_exists("Application\Src\AdvancedAttributeFilters",$handleName)){
            \Application\Src\AdvancedAttributeFilters::$handleName($this,$this->groupList);
        }

		$ilr = new SearchResult($columns, $this->groupList, URL::to('/advanced_asset_pickers/group_search/submit'));
		$this->result = $ilr;
	}

	public function getSearchResultObject() {
		return $this->result;
	}

	public function submit() {
		$this->search();
		$result = $this->result;
		Loader::helper('ajax')->sendResult($this->result->getJSONObject());
	}
	
}

