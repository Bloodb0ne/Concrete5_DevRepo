<?php   

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Package\AdvancedAssetPickers\Attribute\MultiuserPicker\Value as MultiuserPickerAttributeTypeValue;

$akID = $this->attributeKey->getAttributeKeyID();
$obj = MultiuserPickerAttributeTypeValue::getById($avID);	

$single = intval($single);
if($single == 1) {
	$mode = "single";
}else{
	$mode = "multi";
}

	
?>

<script type="text/javascript">

ccm_launchUserManager_<?php   echo $akID; ?> = function() {
	CustomAjaxUserSelector.launchDialog(userCallback<?php   echo $akID ?>,{
		handle: "<?php   echo $akHandle; ?>",
		multipleSelection: <?php   echo $single; ?>
	});
}

ccm_launchGroupManager_<?php   echo $akID; ?> = function() {
	CustomAjaxGroupSelector.launchDialog(groupCallback<?php   echo $akID ?>,{
		handle: "<?php   echo $akHandle; ?>",
		multipleSelection: <?php   echo $single; ?>
	});
}

</script>

<div class="asset-selector-container" data-file-selector="<?php   echo $akID; ?>" onclick='ccm_launchUserManager_<?php   echo $akID; ?>();'>
	<div class="asset-selector">
		<?php   echo t("Select User &raquo;"); ?>
	</div>
</div>

<div class="asset-selector-container" data-file-selector="<?php   echo $akID; ?>" onclick='ccm_launchGroupManager_<?php   echo $akID; ?>();'>
	<div class="asset-selector">
		<?php   echo t("Select Group &raquo;"); ?>
	</div>
</div>

<?php   echo t("Users:"); ?>
<div class="itemPickerContainer" id="ak<?php   echo $akID ?>_assetUsersList">
	<?php   
		foreach($obj->getUsers() as $user){
			Loader::packageElement("user_entry","advanced_asset_pickers",
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"mode"		=>	$mode,
					"akID"		=>	$akID,
					"userID"	=>	$user->getUserID(),
					"userName"	=>	$user->getUserName()));
		}
	?>
</div>
<?php   echo t("Groups:"); ?>
<div class="itemPickerContainer" id="ak<?php   echo $akID ?>_assetGroupsList">
	<?php   
		foreach($obj->getGroups() as $group){
			Loader::packageElement("group_entry","advanced_asset_pickers",
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"mode"		=>	$mode,
					"akID"		=>	$akID,
					"groupID"	=>	$group->getGroupID(),
					"groupName"	=>	$group->getGroupName()));
		}
	?>
</div>

<?php   
	ob_start();
	
	Loader::packageElement("user_entry","advanced_asset_pickers", 
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"mode"		=>	$mode,
					"akID"		=>	$akID,
					"userID"	=>	"'+item.uID+'",
					"userName"	=>	"'+item.displayName+'"));
	$elementTemplate = ob_get_contents();
	ob_end_clean();

	$templateUser = json_encode($elementTemplate);

	ob_start();
	
	Loader::packageElement("group_entry","advanced_asset_pickers", 
				array(
					"controller"=>	$this->controller,
					"templType"	=>	"js",
					"mode"		=>	$mode,
					"akID"		=>	$akID,
					"groupID"	=>	"'+item.gID+'",
					"groupName"	=>	"'+item.gName+'"));
	$elementTemplate = ob_get_contents();
	ob_end_clean();
	
	$templateGroup = json_encode($elementTemplate);
	
 ?>


<script type="text/javascript">

	$(document).ready(function(){
		$('#ak'+<?php   echo $akID; ?>+'_assetUsersList').sortable({ 
			tolerance: "pointer",
			items:'> .userEntry',
			handle: 'i.move-handler',
			cursor: "move" ,
			opacity: 0.5,
			zIndex: 2000,
			containment:'#ak<?php   echo $akID; ?>_assetUsersList'});

		$('#ak'+<?php   echo $akID; ?>+'_assetGroupsList').sortable({ 
			tolerance: "pointer",
			items:'> .groupEntry',
			handle: 'i.move-handler',
			cursor: "move" ,
			opacity: 0.5,
			zIndex: 2000,
			containment:$('#ak<?php   echo $akID; ?>_assetGroupsList')});
	});
	
	userCallback<?php   echo $akID ?> = function(data){

		CustomAjaxUserSelector.getUserDetails(data.uID,function(data){
			<?php   if($mode == "single"): ?>
					$('#ak'+<?php   echo $akID; ?>+'_assetUsersList').html("");
			<?php   endif; ?>
			if(data.users){
				$.each(data.users,function(idx,item){
					var newEntry = '<?php   echo $templateUser; ?>';
					$('#ak'+<?php   echo $akID; ?>+'_assetUsersList').append(newEntry.replace(/"/g, "")); 
				});
			}
		});
	}
	
	groupCallback<?php   echo $akID ?> = function(data){
		<?php   if($mode == "single"): ?>
				$('#ak'+<?php   echo $akID; ?>+'_assetGroupsList').html("");
		<?php   endif; ?>
		if(data.groups){
			$.each(data.groups,function(idx,item){
				console.log(item);
				var newEntry = '<?php   echo $templateGroup; ?>';
				$('#ak'+<?php   echo $akID; ?>+'_assetGroupsList').append(newEntry.replace(/"/g, "")); 
			});
		}
	}
	
</script>