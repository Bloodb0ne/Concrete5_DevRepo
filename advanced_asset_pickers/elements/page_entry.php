<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
$nv = Loader::Helper('navigation');
?>
<div class="pageEntry" id="<?php   echo $akID; ?>_pageAttachmentRow<?php   echo $pageID ?>">
	<div>
	<?php   if($mode == "multi"): ?>
		<?php   echo $form->checkbox($controller->field('pageIDs')."[]",$pageID,true); ?>
	<?php   else: ?>
		<?php   echo $form->hidden($controller->field('pageIDs')."[]",$pageID); ?>
	<?php   endif; ?>

	<span>
		<a class="title" href="<?php   echo $nv->getLinkToCollection(Page::getByID($pageID)); ?>" target="_blank">
			<?php   echo $pageName; ?>
		</a>	
	</span>
	<i class="move-handler"></i>
	</div>
</div> 