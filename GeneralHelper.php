<?php 

namespace	Application\Src;

use Concrete\Core\Page\PageList;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Attribute\Key\CollectionKey as CollectionAttributeKey;
use Concrete\Core\Attribute\Type as AttributeType;
use Concrete\Attribute\Select\Controller as SelectAttributeTypeController;

class GeneralHelper{

	public function parseGalleryItems($items){
		$galleryItems = [];
		foreach ($items as $value) {
            if(is_object($value)){
                $galleryItems[] = [
                    'path' => $value->getRelativePath(),
                    'image' => self::outputThumbnail($value,'newsInner',[],false), 
                    'type' => 'image' 
                ];
            }else{
                $parts = self::parseYoutubeLink($value);
                $galleryItems[] = [
                    'path' => $value,
                    'pathInner' => 'https://www.youtube.com/embed/'.$parts['v'],
                    'type' => 'video' 
                ];

            }
        }

        return $galleryItems;
	}
	public static function isInPath($path,$page){
		return strncmp($path,$page->getCollectionPath(),strlen($path)) == 0;
	}

	public static function isFilterSelected($type,$name,$filters){
		if($filters[$type] == $name){
			return 'selected';
		}else{
			return '';
		}
	}

	public static function hasKeyErrored($key,$fields){
		if(is_null($fields)) return false;
		$keyField = $key->getController()->field('value');
		return in_array($keyField,$fields);
	}

	public static function getPage($ctHandle){
		$pl = new PageList();
		 
		//Filters
		$pl->filterByPageTypeHandle($ctHandle);
		  
		//Get the page List Results 
		$res =  $pl->get(1);
		return $res[0];
	}

	public static function getChildPage($cID,$ctHandle){
		$pl = new PageList();
		 
		//Filters
		$pl->filterByParentID($cID);
		$pl->filterByPageTypeHandle($ctHandle);
		  
		//Get the page List Results 
		$res =  $pl->get(1);
		return $res[0];
	}

	public static function getContactsPage(){
		$currentSection = Section::getCurrentSection();
		if($currentSection){
			$localeID = $currentSection->getCollectionID(); 
		}else{
			return false;
		}
		return self::getChildPage($localeID,"contacts");
	}

	public static function getNewsPage(){
		$currentSection = Section::getCurrentSection();
		if($currentSection){
			$localeID = $currentSection->getCollectionID(); 
		}else{
			return false;
		}
		return self::getChildPage($localeID,"news");
	}


	public static function getCollectionSelectValues($attrHandle=''){
		// Loader::model('attribute/type');
		// Loader::model('attribute/categories/collection');
		$ak = CollectionAttributeKey::getByHandle($attrHandle);
		$satc = new SelectAttributeTypeController(AttributeType::getByHandle('select'));
		$satc->setAttributeKey($ak);
		return $satc->getOptions();
	}

	public static function filteredLink($base,$filters,$filter){
		foreach ($filter as $key => $value) {
			$filters[$key] = $value;
		}

		$filterLink = '';
		foreach ($filters as $key => $value) {
			$filterLink .= "/{$key}:$value";
		}

		return $base.$filterLink;
	}

	public static function parseYoutubeLink($url){
		$url_data = parse_url($url);
		$query = $url_data['query'];

		$result = array();

		$data = explode("&",$query);
		foreach($data as $element){
			$temp = explode("=",$element);
			$result[$temp[0]] = $temp[1];
		}
		
		return $result;
	}

	public static function outputThumbnail($image,$thumbName,$args = array(),$output = true){

		$type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle($thumbName);
	    if (is_object($type) && is_object($image)) {
	        $src = $image->getThumbnailURL($type->getBaseVersion());
	       	$alt = $image->getDescription();

	       	$attribs = "";
	       	foreach($args as $key => $attrValue){
	       		if($attrValue == null){
	       			$attribs .= "{$key} ";
	       		}else{
	       			$attribs .= "{$key} = \"{$attrValue}\" ";
	       		}
	       	}
	       	$img = "<img src=\"{$src}\" alt=\"{$alt}\" {$attribs} >";
	       	
	       	if($output){
	       		print $img;
	       	}else{
	       		return $img;
	       	}

	    }  
	}
}

?>