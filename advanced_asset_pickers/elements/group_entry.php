<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
?>
<div class="groupEntry" id="<?php   echo $akID; ?>_groupAttachmentRow<?php   echo $groupID ?>">
	<div>
	<?php   if($mode == "multi"): ?>
		<?php   echo $form->checkbox($controller->field('groupIDs')."[]",$groupID,true); ?>
	<?php   else: ?>
		<?php   echo $form->hidden($controller->field('groupIDs')."[]",$groupID); ?>
	<?php   endif; ?>
	<span>
		<a class="title" href="javascript:void(0);" target="_blank"><?php   echo $groupName; ?></a>	
	</span>
	<i class="move-handler"></i>
	</div>
</div> 