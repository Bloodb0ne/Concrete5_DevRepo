<?php  
namespace Concrete\Package\AdvancedAssetPickers\Controller\Search;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use FilePermissions;
use Loader;
use \Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Files as SearchFilesController;
class File extends BackendInterfaceController {

	protected $viewPath = '/dialogs/file/search';

	protected function canAccess() {
		$cp = FilePermissions::getGlobal();
		return $cp->canSearchFiles();
	}

	public function view() {
		$cnt = new SearchFilesController();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
        $this->requireAsset('select2');
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

