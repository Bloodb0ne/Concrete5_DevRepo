<?php    

namespace Concrete\Package\IconSelector;

use Theme;
use Package;
use BlockType;
use BlockTypeSet;
use SinglePage;
use Core;
use Page;
use PageTemplate;
use PageType;
use Route;
use Group;
use View;
use Database;
use FileSet;
use Loader;
use Config;
use Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use Concrete\Core\Attribute\Type as AttributeType;
use AssetList;


class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'icon_selector';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';
	

	public function getPackageDescription() {
		return t("Adds an select attribute with icons.");
	}
	
	public function getPackageName() {
		return t("Icon Select Attribute");
	}

	public function install() {
		$pkg = parent::install();

		\Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');

		
		$at = AttributeType::add('icon_selector', t('Icon Selector'), $pkg);
		$col->associateAttributeKeyType($at);

		
	}
}