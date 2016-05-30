<?php 

namespace Concrete\Package\FavoritePages\Src;
use Concrete\Core\User\User;
use Concrete\Core\Controller\Controller;
use Route;
use Loader;

use Database;

class FavoritePage extends Controller{

	public function favorite(){
		header('Content-Type: application/json');
		$u = new User();
		$data = $this->post();
		
		$pageID = (int)$this->get('id');
		$p = \Page::getByID($pageID);
		
		if($u->getUserID() && !$p->isError() && $p->isSystemPage() == false){

			//If user favorited remove entry else add entry
			$db = Database::get();
			$res = $db->GetRow("SELECT Count(cID) as num FROM UserPageFavorites WHERE uID=? and cID=?",array((int)$u->getUserID(),$pageID));
			if($res['num'] == 1){
				//Favorite Product
				$db->Execute('DELETE FROM UserPageFavorites WHERE cID=? and uID=?',array($pageID,(int)$u->getUserID()));
				echo json_encode(array('status'=>'unfavorited'));
			}else{
				//Unfavorite Product
				$db->Execute('INSERT INTO UserPageFavorites(cID,uID) VALUES(?,?)',array($pageID,(int)$u->getUserID()));
				echo json_encode(array('status'=>'favorited'));
			}
			exit();

		}else{
			echo json_encode(array('status'=>'not_logged'));
		}
	}
}
 ?>