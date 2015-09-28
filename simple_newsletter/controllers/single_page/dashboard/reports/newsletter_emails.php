<?php 

namespace Concrete\Package\SimpleNewsletter\Controller\SinglePage\Dashboard\Reports;

use \Concrete\Core\Page\Controller\DashboardPageController;
use Database;
use Concrete\Package\SimpleNewsletter\Src\Newsletter\EmailList as NewsletterEmailsList;
use Concrete\Package\SimpleNewsletter\Src\Newsletter\Email as NewsletterEmail;

defined('C5_EXECUTE') or die("Access Denied.");

class NewsletterEmails extends DashboardPageController{

	protected $helpers = ['form'];

	public function deleteAll(){
		$nmail_list = new NewsletterEmailsList();
		$pagination = $nmail_list->getPagination();
		$subscriptions = $pagination->getCurrentPageResults();
		
		foreach ($subscriptions as $value) {
			$value->delete();
		}

		$this->redirect("dashboard/reports/newsletter_emails/");
	}

	public function delete($eID){
		$nEmail = NewsletterEmail::getByID($eID);
		$nEmail->delete();
		$this->redirect("dashboard/reports/newsletter_emails/");
	}

	public function view(){

		$nmail_list = new NewsletterEmailsList();
		$nmail_list->setItemsPerPage(1);
		// $nmail_list->sortByCreationDate('desc');
		$pagination = $nmail_list->getPagination();
		$subscriptions = $pagination->getCurrentPageResults();
		
		$this->set('paginator',$pagination);
		$this->set('subscriptions',$subscriptions);
	}

	public function download_list(){
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="email_list.csv"');

		$nmail_list = new NewsletterEmailsList();
		$nmail_list->sortByCreationDate('desc');
		$pagination = $nmail_list->getPagination();
		$subscriptions = $pagination->getCurrentPageResults();

		echo "Email,IP,Created,Confirmed\n";
		foreach($subscriptions as $subscriber){
			echo $subscriber->getEmail().",";
			echo $subscriber->getIP().",";
			echo $subscriber->getDateCreated().",";
			echo $subscriber->getDateConfirmed()."\n";
		}
		exit();
	}

	public function download_confirmed(){
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="email_confirmed_list.csv"');

		$nmail_list = new NewsletterEmailsList();
		$nmail_list->sortByCreationDate('desc');
		$nmail_list->filterOnlyConfirmed();
		$pagination = $nmail_list->getPagination();
		$subscriptions = $pagination->getCurrentPageResults();

		echo "Email,IP,Created,Confirmed\n";
		foreach($subscriptions as $subscriber){
			echo $subscriber->getEmail().",";
			echo $subscriber->getIP().",";
			echo $subscriber->getDateCreated().",";
			echo $subscriber->getDateConfirmed()."\n";
		}
		exit();
	}


}

?>