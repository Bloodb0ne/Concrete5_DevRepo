
<?php   
defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Type\Type as CollectionType;
$form = Loader::helper('form');

//Get all attributes
$types = CollectionType::getList();
$options = array("" => "*None*");
array_map(function($type) use (&$options){
	$handle = $type->getPageTypeHandle();
	$name = $type->getPageTypeName();
	$options[$handle] = $name;
},$types);

?>

<script type="text/javascript">

ccm_launchPageManager = function() {
	ConcretePageAjaxSearch.launchDialog(function(data){
		$("#pageSelection .selected-item-label").html(data.title);
		$("#pageSelection #parentId").val(data.cID);
	},{});
}

removeSelection = function(){
	$("#pageSelection .selected-item-label").html("");
	$("#pageSelection #parentId").val(-1);
}
</script>

<fieldset>
	<legend><?php  echo t("Attribute Options"); ?></legend>

	<div class="form-group">

		<div class="checkbox">
			<label>
				<?php   echo $form->checkbox("preserveOrder",1,$preserveOrder); ?>
				<?php  echo t("Perserve Order of Values"); ?>
			</label>
		</div>

		<div class="checkbox">
			<label>
			<?php   echo $form->checkbox("uniqueValues",1,$uniqueValues); ?>
			<?php  echo t("Unique Values"); ?>
			</label>
		</div>
		
	</div>

	<div class="form-group">
		<?php   echo $form->label("single",t("Selection Type")); ?>
		<?php  echo $form->select("single",[1=>'Single',0=>'Multi'],$single) ?>
	</div>

	<div class="form-group">
		<?php   echo $form->label("typeHandle",t("Filter by PageType")); ?>
		<?php   echo $form->select("typeHandle",$options,$typeHandle); ?>
	</div>

	<div class="form-group">
		<?php   echo $form->label("path",t("Filter by Path")); ?>
		<?php   echo $form->text("path",$path); ?>
	</div>

	<div class="form-group">
		<?php   echo $form->label("parentId",t("Filter by Parent Page")); ?>
	</div>
	
	<?php   
		if($parentId != -1){
			$parent = Page::getByID($parentId);
			$name = $parent->getCollectionName();
		}else{
			$name = "";
		}
	?>

	<div id="pageSelection" class="asset-selector">
		<div class="">
			<strong class="selected-item-label"><?php   echo $name; ?></strong>
		</div>
		<a class="ccm-sitemap-select-page"  href="javascript:void(0);" onClick="ccm_launchPageManager(); false;"><?php  echo t('Select Page'); ?></a>

		<a href="javascript:void(0)" onClick="removeSelection();"  class='remove-icon'>
			<img src="<?php   echo DIR_REL; ?>/concrete/images/icons/remove.png" >
		</a>
		<?php   echo $form->hidden("parentId",$parentId); ?>
	</div>

	<?php   
		$handle = Loader::helper("text")->camelcase($akHandle);
		$filterName = "filter".$handle;
		if($handle):
	?>
		<h4><?php  echo t("Name of the filter function: ").$filterName; ?></h4>	
		<div><?php  echo t('Example'); ?>:</div>
		<code style='display:block;'>
			<span style="color: #FF9400">
				<span style="color: #2956DB">public&nbsp;static function&nbsp;</span>
				<span style="color: #489D45"><?php  echo $filterName; ?></span><span style="color: #808080">(</span>
				<span style="color: #000000">&amp;$srchController</span><span style="color: #808080">,</span>
				<span style="color: #000000">&amp;$pageList</span><span style="color: #808080">){<br></span>
				<span style="color: #0080FF">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color: #000000">$srchController-&gt;searchBar&nbsp;</span>
				<span style="color: #808080">=&nbsp;</span><span style="color: #000000">false</span>
				<span style="color: #808080">;<br></span>
				<span style="color: #0080FF">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color: #C0C0C0">//&nbsp;$pageList-&gt;setItemsPerPage(1);<br></span>
				<span style="color: #808080">}<br></span>
			</span>
		</code>
		
		
	<?php  endif; ?>
</fieldset>



