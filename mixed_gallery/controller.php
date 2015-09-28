<?php    

namespace Concrete\Package\MixedGallery;

use Concrete\Core\Package\Package;
use Concrete\Core\Page\Theme\Theme;
use Route;
use AssetList;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Type as AttributeType;

defined('C5_EXECUTE') or die("Access Denied.");

class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'mixed_gallery';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';

	public function on_start() {
		Route::register('/dialog/videolink','Concrete\Package\MixedGallery\Controller\VideoLink::view');
	}
	public function getPackageDescription() {
		return t("Contains a attribute with a mixed content gallery(links+images)");
	}
	
	public function getPackageName() {
		return t("Mixed Gallery Attribute");
	}
	
	public function install() {
		$pkg = parent::install();
		\Loader::model('attribute/categories/collection');
		$col = AttributeKeyCategory::getByHandle('collection');

		//Install attribute and attach to the Colletion Category		
		$gallery = AttributeType::add('mixed_gallery', t('Mixed Gallery'), $pkg);
		$col->associateAttributeKeyType($gallery);

	}
}