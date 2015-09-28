<?php
namespace Concrete\Package\SimpleNewsletter\Src\Newsletter;

use Concrete\Package\SimpleNewsletter\Src\Newsletter\Email as NewsletterEmail;
use Database;
use Concrete\Core\Search\Pagination\Pagination;
use Concrete\Core\Search\ItemList\Database\ItemList;
use Pagerfanta\Adapter\DoctrineDbalAdapter;

class EmailList extends ItemList {

	public function createQuery()
    {
        $this->query
        ->select('e.eID')
        ->from('pkgNewsletterEmails','e');
    }

    public function sortByCreationDate($order = 'asc') {
        $this->query->orderBy('e.created',$order);
    }

    public function sortByConfirmationDate($order = 'asc') {
        $this->query->orderBy('e.confirmed',$order);
    }

    public function filterOnlyConfirmed(){
        $this->query->andWhere('e.confirmed IS NOT NULL');
    }

    public function filterOnlySubscribed(){
        $this->query->andWhere('e.unsubscribed = 0');
    }

    public function filterOnlyUnsubscribed(){
        $this->query->andWhere('e.unsubscribed = 1');
    }


    public function getResult($queryRow)
    {
        return NewsletterEmail::getByID($queryRow['eID']);
    }
    
    protected function createPaginationObject()
    {
        $adapter = new DoctrineDbalAdapter($this->deliverQueryObject(), function ($query) {
            $query->select('count(distinct e.eID)')->setMaxResults(1);
        });
        $pagination = new Pagination($this, $adapter);
        return $pagination;
    }
    
    public function getTotalResults()
    {
        $query = $this->deliverQueryObject();
        return $query->select('count(distinct e.eID)')->setMaxResults(1)->execute()->fetchColumn();
    }
}