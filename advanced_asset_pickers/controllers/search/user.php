<?php  
namespace Concrete\Package\AdvancedAssetPickers\Controller\Search;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Loader;
class User extends BackendInterfaceController {

	protected $viewPath = '/dialogs/user/search';

	protected function canAccess() {
		$tp = Loader::helper('concrete/user');
		return $tp->canAccessUserSearchInterface();
	}

	public function view() {
		$cnt = new \Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Users();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
        $this->requireAsset('select2');
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

