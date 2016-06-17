<?php 

namespace Application\Src;

use Application\Src\FavoritedPageList;

use Concrete\Core\Multilingual\Page\PageList as MultilingualPageList;
use Concrete\Core\Page\PageList;
use User;
use Database;

class LangFavoritePageList extends PageList{

	public function getResult($queryRow){
		$c = parent::getResult($queryRow);
        $c->favorite = $queryRow['favorite'];
		return $c;
	}


	public function deliverQueryObject()
    {
    	$query = parent::deliverQueryObject();
        $u = new User();
        
        if($u->isLoggedIn()){
            $uID = $u->getUserID();
        }else{
            $uID = -1;
        }

        if($uID != -1){
            // $query->leftJoin('p', 'UserPageFavorites', 'fav', 'p.cID = fav.cID');
            // $query->addSelect('fav.uID IS NOT NULL as favorite');
            // $query->leftJoin('p', 'UserPageFavorites', 'fav', 'p.pID = fav.pID and fav.uID = '.(int)$u->getUserID());

            $relation = Database::get()->createQueryBuilder();
            $relation->select('mpRelationID')->
                from('MultilingualPageRelations', 'mppr')->
                leftJoin('mppr', 'UserPageFavorites', 'fav', 'mppr.CID = fav.cID WHERE fav.uID = :uID')->
                setParameter('uID',(int)$uID);
            //     // ->
                // andWhere('fav.uID = :uID');

            $query->leftJoin('p', 'MultilingualPageRelations', 'mmpr2', 'p.cID = mmpr2.cID');
            $query->addSelect('mmpr2.mpRelationID');
            // $query->andWhere("mmpr2.mpRelationID in (" . $relation . ")");
            $query->addSelect(' mmpr2.mpRelationID in (' . $relation . ') as favorite');
            $query->andWhere("mmpr2.mpLocale = :loc");
            $query->setParameter('uID',(int)$uID);
            $query->setParameter('loc',\Localization::activeLocale());
            var_dump(\Localization::activeLocale());
            // echo $query->getSql();
        }

        

        

        return $query;
    }

}


?>