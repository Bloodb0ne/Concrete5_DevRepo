<?php      

namespace Concrete\Package\AdvancedAssetPickers;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Type as AttributeType;
use Route;
use AssetList;
use Loader;
use Database;


defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'advanced_asset_pickers';
	protected $appVersionRequired = '5.7.0.4';
	protected $pkgVersion = '0.2';
	
	public function on_start() {

	    Route::register('/advanced_asset_pickers/user_search', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\User::view');
	    Route::register('/advanced_asset_pickers/group_search', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Group::view');
	    Route::register('/advanced_asset_pickers/page_search', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Page::view');
	    Route::register('/advanced_asset_pickers/file_search', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\File::view');

	    Route::register('/advanced_asset_pickers/group_search/submit', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Groups::submit');
		Route::register('/advanced_asset_pickers/user_search/submit/{handle}', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Users::submit');
		Route::register('/advanced_asset_pickers/page_search/submit/{handle}/{key}', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Pages::submit');
		Route::register('/advanced_asset_pickers/file_search/submit/{handle}', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Files::submit');
		
		Route::register('/advanced_asset_pickers/page_search/get_pages', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Pages::getPagesJSON');
		Route::register('/advanced_asset_pickers/user_search/get_users', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Users::getUsersJSON');
		Route::register('/advanced_asset_pickers/group_search/get_groups', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Users::getGroupsJSON');
		// Route::register('/advanced_asset_pickers/file_search/get_files', '\Concrete\Package\AdvancedAssetPickers\Controller\Search\Dialog\Users::getFilesJSON');


		$al = AssetList::getInstance();
	    $al->register(
	        'javascript', 'page_selector', 'js/page_selector.js',[],$this
	    );

	    $al->register(
	        'javascript', 'user_selector', 'js/user_selector.js',[],$this
	    );

	    $al->register(
	        'javascript', 'group_selector', 'js/group_selector.js',[],$this
	    );

	     $al->register(
	        'javascript', 'file_selector', 'js/file_selector.js',[],$this
	    );

	    $al->register(
	        'css', 'asset_picker_style', 'css/asset_pickers_main.css?5',[],$this
	    );

	    
	}
	public function getPackageDescription() {
		return t("Contains Page, User and File Picker attribute types.");
	}
	
	public function getPackageName() {
		return t("Advanced Multi/Single Asset Pickers");
	}
	
	private function addAttributes($pkg){
		\Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');

		
		$fileSelector = AttributeType::add('multifile_picker', t('Multiple File Picker'), $pkg);
		$col->associateAttributeKeyType($fileSelector);

		$userSelector = AttributeType::add('multiuser_picker', t('Multiple User Picker'), $pkg);
		$col->associateAttributeKeyType($userSelector);

		$pageSelector = AttributeType::add('multipage_picker', t('Multiple Page Picker'), $pkg);
		$col->associateAttributeKeyType($pageSelector);
	}
	
	public function install() {
		$pkg = parent::install();
		$this->addAttributes($pkg);
	}

	

}