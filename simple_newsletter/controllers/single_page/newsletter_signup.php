<?php 

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage;
use Concrete\Package\SimpleNewsletter\Src\Newsletter\Email as NewsletterEmail;
use \Concrete\Core\Page\Controller\PageController;

class NewsletterSignup extends PageController{

	public function subscribe(){
		if($this->isPost()){
			$json = $this->post('json',false);

			$data = array();

			$data['ip'] = \Core::make('helper/validation/ip')->getRequestIP()->getIp();
			$data['email'] = $this->post('email');

			$error = NewsletterEmail::validate($data);
			if(!$error->has()){
				NewsletterEmail::add($data);
				$state = 'success';
			}else{
				$state = 'failure';
				$list = $error->getList();

				if($json){
					header('Content-Type: application/json');
					echo json_encode(array('errors'=>$list));
					exit();
				}else{
					$this->set('state',$state);
					$this->set('errors',$list);
				}
			}

		}
	}

	public function unsubscribe(){
		$email = urldecode($this->get('e'));
		$hash = urldecode($this->get('h'));
		$json = $this->get('json',false);

		if($email && $hash){
			$nEmail = NewsletterEmail::getByEmail(urldecode($email));
			if($nEmail->eID){
				//
				$nEmail->unsubscribed = 1;
				$nEmail->save();

				$state = 'success';
			}else{
				$state = 'failure';
			}

			$this->set('state','failure');
		
			if($json){
				header('Content-Type: application/json');
				echo json_encode(array('state'=>$state));
				exit();
			}
		}
	}

}

 ?>