<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
?>
<div class="userEntry" id="<?php   echo $akID; ?>_pageAttachmentRow<?php   echo $userID ?>">
	<div>
	<?php   if($mode == "multi"): ?>
		<?php   echo $form->checkbox($controller->field('userIDs')."[]",$userID,true); ?>
	<?php   else: ?>
		<?php   echo $form->hidden($controller->field('userIDs')."[]",$userID); ?>
	<?php   endif; ?>
	<span>
		<a class="title" href="javascript:void(0);" target="_blank"><?php   echo $userName; ?></a>	
	</span>
	<i class="move-handler"></i>
	</div>
</div> 