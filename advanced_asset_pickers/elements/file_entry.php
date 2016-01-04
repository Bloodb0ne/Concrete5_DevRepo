<?php   
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper("form");
?>
<div class="fileEntry" id="<?php   echo $akID; ?>_fileAttachmentRow<?php   echo $fileID ?>">
	<div>
	<?php   echo $form->checkbox($controller->field('fileIDs')."[]",$fileID,true); ?>
	<?php   if($templType == "js"): ?>
		<?php   echo $fileThumb; ?>

	<?php   elseif($templType == "inline"): 
		$image = File::getById($fileID);
		$ih = Loader::Helper('image');
		$ext = $image->getExtension();
		
		if(in_array($ext,['jpeg','png','gif','jpg'])){
			$thumb = $ih->getThumbnail($image,32,32,true);
			echo "<img src='{$thumb->src}'>";
		}elseif(file_exists(DIR_AL_ICONS."/{$ext}.png")){
			$path = \Core::getApplicationRelativePath()."/concrete/images/icons/filetypes/{$ext}.png";
			echo "<img width='32' height='32' src='{$path}'>";
		}
	?>
		
	<?php   endif; ?>
	<span>
		<a class="title" href="<?php   echo $filePath; ?>" target="_blank"><?php   echo $fileTitle; ?></a>	
		<i class="move-handler"></i>
	</span>
	
	</div>
</div> 