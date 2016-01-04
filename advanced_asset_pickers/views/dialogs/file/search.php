<?php  
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="files" class="ccm-ui">
<?php   Loader::packageElement('files/search','advanced_asset_pickers', array('controller' => $searchController))?>
</div>

<script type="text/javascript">
$(function() {
	$('div[data-search=files]').customFileManager({
		result: <?php   echo $result?>,
		mode: '<?php   echo $searchController->getSelectionMode(); ?>'
	});
});
</script>