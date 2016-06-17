<?php
defined('C5_EXECUTE') or die("Access Denied.");
//1.Select View Type
//2.Show appropriate fields to edit
// Just show/hide fields that are compatable with the type
$form = \Core::make('helper/form');
// var_dump($controller->getViewType());
?>
<div class='ccm-ui ccm-inline-toolbar' style='width:50%'>

<div class="pull-right">
	<button type='button' class='btn btn-toolbar formControl cancelBlock'> Cancel </button>
	<button type='button' class='btn btn-toolbar btn-primary formControl saveBlock'> Save </button>
</div>
</div>
<div class="clearfix"></div>
<div class="typeFields" style='padding:5px;'>
	<?php echo $form->select('view_type',[
	'1'=>'Full Text',
	'2'=>'Left Image Right Text',
	'3'=>'Right Image Left Text',
	'4'=>'Two Image'],$controller->getViewType(),['style'=>'display:inline-block;width:80%;','class'=>'mixedContentBlockType']); ?>
	<?php 
		$al = Core::make('helper/concrete/asset_library');
		// Create overlay that hides the image 
        $form = '<div class="ccm-attribute ccm-attribute-image-file left-file" style="display:none;">';
        $form .= $al->image('left_file','image_left', t('Choose Image'),\File::getByID($controller->getLeftImage()));
        $form .= '</div>';
        
        print $form;

        $form = '<div class="ccm-attribute ccm-attribute-image-file right-file" style="display:none;">';
        $form .= $al->image('right_file','image_right', t('Choose Image'),\File::getByID($controller->getRightImage()));
        $form .= '</div>';

        print $form;

		print Core::make("editor")->outputStandardEditor('content',$controller->getContentEditMode());

        
	?>
</div>

<script type="text/javascript">
function filterFields(type){
	switch(type){
			case '1':
				$('[name=content]').parent().fadeIn();
				$('.left-file').fadeOut();
				$('.right-file').fadeOut();
				break;
			case '2':
				$('[name=content]').parent().fadeIn();
				// $('[name=content]').parent().width('50%');
				$('.left-file').fadeIn();
				$('.right-file').fadeOut();
				break;
			case '3':
				$('[name=content]').parent().fadeIn();
				$('.left-file').fadeOut();
				$('.right-file').fadeIn();
				break;
			case '4':
				$('[name=content]').parent().fadeOut();
				$('.left-file').fadeIn();
				$('.right-file').fadeIn();
				break;
		}
}
$(document).ready(function(){
	filterFields('<?php echo $controller->getViewType(); ?>');
	$('.mixedContentBlockType').on('change',function(){
		filterFields($(this).val());
	})

	$('.cancelBlock').on('click',function(e){
		e.preventDefault();
		ConcreteEvent.fire('EditModeExitInline');
	})

	$('.saveBlock').on('click',function(e){
		e.preventDefault();
		ConcreteEvent.fire('EditModeBlockSaveInline');
	})
});
</script>