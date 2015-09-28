<?php 
namespace Concrete\Package\MixedGallery\Controller;

use \Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Loader;

class VideoLink extends BackendInterfaceController{

	protected $viewPath = '/dialogs/video_link';

	public function canAccess(){
		return true;
	}
	public function view(){

		$this->set('result',[]);
	}
}
 ?>