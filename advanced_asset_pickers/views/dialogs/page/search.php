<?php  
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search-pages="<?php   echo $timestamp?>" class="ccm-ui">
<?php   Loader::packageElement('pages/search','advanced_asset_pickers', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {

	$('div[data-search-pages=<?php   echo $timestamp?>]').customAjaxPageSelector({
		result: <?php   echo $result?>,
		mode: '<?php   echo $searchController->getSelectionMode(); ?>',
		avID: '<?php   echo $searchController->attributeValueID; ?>'
	});
});
</script>

<style type="text/css">
	div[data-search=pages].ccm-ui form.ccm-search-fields {
		margin-left: 0px;
	}
</style>