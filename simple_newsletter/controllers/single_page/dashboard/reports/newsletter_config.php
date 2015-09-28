<?php  defined('C5_EXECUTE') or die(_("Access Denied."));

class DashboardReportsNewsletterConfigController extends Controller {

	protected $helpers = ['form'];

	protected function getMailchimpLists($key,$current_id){
        Loader::library('mailchimp');
        $mc = new \Drewm\MailChimp($key);
        $result = $mc->call('lists/list');

        $lists = $result['data'];

        $options = "";
        foreach ($lists as $list) {
            $id = $list['id'];
            $name = $list['name'];
            if($id == $current_id){
                $selected = "selected";
            }else{
                $selected = "";
            }
            $options .= "<option value={$id} {$selected}>{$name}</option>";
        }
        return $options;
    }

    public function on_start(){
        $pkg = Package::getByHandle('newsletter_manager');

        $this->set('lists',$this->getMailchimpLists($pkg->config('NEWSLETTER_MAILCHIMP_API_KEY'),$pkg->config('NEWSLETTER_MAILCHIMP_LIST')));

        $this->set('NEWSLETTER_MAILCHIMP_API_KEY', $pkg->config('NEWSLETTER_MAILCHIMP_API_KEY'));
        $this->set('NEWSLETTER_MAILCHIMP_LIST', $pkg->config('NEWSLETTER_MAILCHIMP_LIST'));
    }

	public function view(){
		
	}

	public function saveConfig(){
        $pkg = Package::getByHandle('newsletter_manager');

		$pkg->saveConfig('NEWSLETTER_MAILCHIMP_API_KEY', $this->post('NEWSLETTER_MAILCHIMP_API_KEY'));
		$pkg->saveConfig('NEWSLETTER_MAILCHIMP_LIST', $this->post('NEWSLETTER_MAILCHIMP_LIST'));

	}


	public function getLists(){
		$api_key = $this->get('key');

        Loader::library('mailchimp');
        $mc = new \Drewm\MailChimp($api_key);
        $result = $mc->call('lists/list');

        $lists = $result['data'];

        $options = "";
        foreach ($lists as $list) {
            $id = $list['id'];
            $name = $list['name'];
            $options .= "<option value={$id}>{$name}</option>";
        }

        echo $options;
        exit();
	}
	
}

?>