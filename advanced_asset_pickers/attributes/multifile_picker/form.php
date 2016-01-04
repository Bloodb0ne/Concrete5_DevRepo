<?php   
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\AdvancedAssetPickers\Attribute\MultifilePicker\Value as MultifilePickerAttributeTypeValue;

$akID = $this->attributeKey->getAttributeKeyID();
$mpatv = MultifilePickerAttributeTypeValue::getById($avID);

$single = intval($single);
if($single == 1) {
	$mode = "single";
}else{
	$mode = "multi";
}

?>


<?php   
$fp = FilePermissions::getGlobal();
if($fp->canAccessFileManager()): 
?>
	<div class="asset-selector-container" onclick='openFileDialog_<?php   echo $akID; ?>();'>
		<div class="asset-selector">
			<?php   echo t('Choose file &raquo;') ?>
		</div>
	</div>
<?php   endif; ?>

<div class="itemPickerContainer" id="ak<?php   echo $akID ?>_assetFilesList">
	<?php   
		foreach($mpatv->getFiles() as $file){
			$fv = $file->getApprovedVersion();
			Loader::packageElement("file_entry","advanced_asset_pickers", 
				array(
					"controller"=>	$this->controller,
					"templType"		=>	"inline",
					"akID"		=>	$akID,
					"fileID"	=>	$file->getFileID(),
					"filePath"	=>	$fv->getRelativePath(),
					"fileTitle" =>	$fv->getTitle() ));
		} 

	?>
</div>
<?php   
	ob_start();
	Loader::packageElement("file_entry","advanced_asset_pickers", 
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"akID"		=>	$akID,
					"fileID"	=>	"'+item.fID+'",
					"filePath"	=>	"'+item.url+'",
					"fileTitle" =>	"'+item.title+'",
					"fileThumb" =>	"'+item.resultsThumbnailImg+'"));
	
	$elementTemplate = ob_get_contents();
	ob_end_clean();
	
	$template = json_encode($elementTemplate);
	

	
 ?>


<script type="text/javascript">

	$(document).ready(function(){
		$('#ak'+<?php   echo $akID; ?>+'_assetFilesList').sortable({ 
			tolerance: "pointer",
			items:'> .fileEntry',
			handle: 'i.move-handler',
			cursor: "move" ,
			opacity: 0.5,
			zIndex: 2000,
			containment:'#ak<?php   echo $akID; ?>_assetFilesList'});
		
	});
	function openFileDialog_<?php  echo $akID; ?>(){
		CustomFileManager.launchDialog(setPickerCallback<?php  echo $akID; ?>(),{
			handle: "<?php   echo $akHandle; ?>",
			multipleSelection:<?php   echo $single; ?>
	    });
		
	}
	function setPickerCallback<?php  echo $akID; ?>(){
		return function (data){

			CustomFileManager.getFileDetails(data.fID,function(data){
				<?php   if($mode == 'single'): ?>
					$('#ak'+<?php  echo $akID; ?>+'_assetFilesList').html('');
				<?php   endif; ?>
				if(data.files){
					$.each(data.files,function(idx,item){
						if(item.genericTypeText != "Image"){
							item.resultsThumbnailImg = "";
						}
						var newEntry = '<?php   echo $template; ?>';
						$('#ak'+<?php  echo $akID; ?>+'_assetFilesList').append(newEntry.replace(/"/g, "")); 
					});
				}
			});
				$('#ak'+<?php  echo $akID; ?>+'_assetFilesList').sortable();
		}
		
	}

</script>