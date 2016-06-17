<?php    

namespace Concrete\Package\FavoritePages;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Theme\Theme;
use Route;
use AssetList;
use Loader;
use Events;


defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'favorite_pages';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';

	public function on_start() {
		Route::register('/favorite_page/{id}','Concrete\Package\FavoritePages\Src\FavoritePage::favorite');
	}
	public function getPackageDescription() {
		return t("Adds a favorite feature to pages");
	}
	
	public function getPackageName() {
		return t("Product Favorite for Pages");
	}
	
	public function install() {
		$pkg = parent::install();
	}

}