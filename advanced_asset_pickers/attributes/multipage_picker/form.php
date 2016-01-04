<?php   
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\AdvancedAssetPickers\Attribute\MultipagePicker\Value as MultipagePickerAttributeTypeValue;

$akID = $this->attributeKey->getAttributeKeyID();
$obj = MultipagePickerAttributeTypeValue::getById($avID);

$single = intval($single);
if($single == 1) {
	$mode = "single";
}else{
	$mode = "multi";
}

?>

<script type="text/javascript">

ccm_launchPageManager<?php   echo $akID; ?> = function() {
	CustomAjaxPageSelector.launchDialog(setPickerCallback<?php   echo $akID ?>(),{
		handle:"<?php   echo $akHandle; ?>",
		multipleSelection: <?php   echo $single; ?>,
		akID: <?php   echo $akID; ?>
	});
}

</script>
<div>

</div>
<div class="asset-selector-container"  onClick="ccm_launchPageManager<?php   echo $akID; ?>(); return false">
	<div class="asset-selector">
		<?php   echo t("Select Page &raquo;"); ?>
	</div>
</div>

<div class="itemPickerContainer" id="ak<?php   echo $akID; ?>_assetPagesList">
	<?php   
		foreach($obj->getPages() as $page){
			Loader::packageElement("page_entry","advanced_asset_pickers",
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"akID"		=>	$akID,
					"mode"		=>	$mode,
					"pageID"	=>	$page->getCollectionID(),
					"pageName"	=>	$page->getCollectionName()));
		}
	?>
</div>

<?php   
	ob_start();
	Loader::packageElement("page_entry","advanced_asset_pickers", 
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"akID"		=>	$akID,
					"mode"		=>	$mode,
					"pageID"	=>	"'+item.cID+'",
					"pageName"	=>	"'+item.name+'"));
	
	$elementTemplate = ob_get_contents();
	ob_end_clean();
	
	$template = json_encode($elementTemplate);
	
 ?>

<script type="text/javascript">

	$(document).ready(function(){
		$('#ak'+<?php   echo $akID; ?>+'_assetPagesList').sortable({ 
			tolerance: "pointer",
			items:'> .pageEntry',
			handle: 'i.move-handler',
			cursor: "move" ,
			opacity: 0.5,
			zIndex: 2000,
			containment:'#ak<?php   echo $akID; ?>_assetPagesList'});
		
	});
	
	function setPickerCallback<?php   echo $akID ?>(){
		return function (data){
			CustomAjaxPageSelector.getPageDetails(data.cID,function(data){
				console.log(data);
				if(data.pages){
					$.each(data.pages,function(idx,item){
						var newEntry = '<?php   echo $template; ?>';
						$('#ak'+<?php   echo $akID ?>+'_assetPagesList').append(newEntry.replace(/"/g, "")); 
					});
				}
			});
			
			$('#ak'+<?php   echo $akID ?>+'_assetPagesList').sortable();
		}
		
	}
	
	
</script>