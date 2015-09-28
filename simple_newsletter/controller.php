<?php 

namespace Concrete\Package\SimpleNewsletter;

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


class Controller extends \Concrete\Core\Package\Package {

	protected $pkgHandle = 'simple_newsletter';
	protected $appVersionRequired = '5.7.0';
	protected $pkgVersion = '0.1';
	

	public function getPackageDescription() {
		return t("A simple package for gathering emails for newsletters");
	}
	
	public function getPackageName() {
		return t("Simple Newsletter");
	}


	public function on_start(){

	}

	public function install(){
		$pkg = parent::install();

		Loader::model('single_page');
			//Install confirmation page
			$confirm_page = SinglePage::add('/confirm_newsletter_signup',$pkg);
			$confirm_page->setAttribute('exclude_nav',1);

			$signup_page = SinglePage::add('/newsletter_signup',$pkg);
			$signup_page->setAttribute('exclude_nav',1);

			//Install dasboard report page
			SinglePage::add('/dashboard/reports/newsletter_emails',$pkg);
	}

	public function uninstall(){
		parent::uninstall();

		//Remove our table data
		$db = Loader::db();
		$db->execute("DROP TABLE pkgNewsletterEmails");
	}
}
?>

