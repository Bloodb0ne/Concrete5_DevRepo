<?php 

namespace Application\Src;

use Concrete\Core\Page\PageList;
use User;

class FavoritedPageList extends PageList{

	public function getResult($queryRow){
		$c = parent::getResult($queryRow);
		$c->isFavorite = $queryRow['favorite'];
		return $c;
	}


	public function deliverQueryObject()
    {
    	$query = parent::deliverQueryObject();
        
        $u = new User();
        if($u->getUserID()){
            $query->addSelect('fav.uID IS NOT NULL as favorite');
            $query->leftJoin('p', 'UserPageFavorites', 'fav', 'p.pID = fav.pID and fav.uID = '.(int)$u->getUserID());
        }

        if($this->favorites == true && $u->getUserID()){
            $query->andWhere('fav.uID IS NOT NULL');
        }



        return $query;
    }
    
    public function onlyFavorites($fav = true){
        $this->favorites = (bool)$fav;
    }
        
}


?>