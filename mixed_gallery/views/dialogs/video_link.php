<?
defined('C5_EXECUTE') or die("Access Denied.");

$url = "";
?>
<div class="ccm-ui">
<?php //$form->label('videoUrl',t('Choose Url')); ?>
<?php echo Loader::helper('form')->text('videoUrl',$url); ?>

<script type="text/javascript">
$(document).ready(function(){
	$('.selectURL').on('click',function(){
		var selectedUrl = $('#videoUrl').val();
		if(selectedUrl == ""){
			alert('URL Cannot be empty.');
		}else{
			var data = {};
			data.url = selectedUrl;
			ConcreteEvent.publish('GalleryChooseUrl',data);
		}
	})
});
</script>

<div class="ui-dialog-buttonpane ui-widget-content ui-helper-clearfix ccm-ui">
	<div>
		<button class="selectURL pull-right btn btn-success" type="button">Choose Video URL</button>
	</div>
</div>


</div>