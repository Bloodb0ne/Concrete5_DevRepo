<?php  
namespace Concrete\Package\AdvancedAssetPickers\Controller\Search;
use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Loader;
class Page extends BackendInterfaceController {

	protected $viewPath = '/dialogs/page/search';

	protected function canAccess() {
		$sh = Loader::helper('concrete/dashboard/sitemap');
		return $sh->canRead();
	}

	public function view() {
		$this->set('timestamp', time());
		$cnt = new \Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Pages();
		$cnt->search();
		$result = Loader::helper('json')->encode($cnt->getSearchResultObject()->getJSONObject());
		$this->set('result', $result);
		$this->set('searchController', $cnt);
	}

}

