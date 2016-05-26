<?php    

namespace Concrete\Package\MixedContentBlock;

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

	protected $pkgHandle = 'mixed_content_block';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';
	

	public function getPackageDescription() {
		return t("Includes an block to mixed content");
	}
	
	public function getPackageName() {
		return t("Mixed Content Block");
	}

	public function install() {
		$pkg = parent::install();

		BlockType::installBlockTypeFromPackage('mixed_content', $pkg);
	}
}