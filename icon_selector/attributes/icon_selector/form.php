<?php defined('C5_EXECUTE') or die("Access Denied."); 

$form = Loader::helper('form');
$json = Loader::helper('json');

$options = $this->controller->getOptions();
//file_manager_listing
if(!function_exists('outputThumbnail')){
	function outputThumbnail($image,$thumbName){
		$type = \Concrete\Core\File\Image\Thumbnail\Type\Type::getByHandle($thumbName);
	    if (is_object($type) && is_object($image)) {
	        return $image->getThumbnailURL($type->getBaseVersion());
	    }
	    return "";  
	}
}

if ($akAllowMultipleValues): ?>
    <select id="ak_<?php echo $akID; ?>_iconSelect" class='iconSelector' name='<?php echo $this->field('atIconSelectOptionID').'[]';  ?>' multiple>
 <?php else: ?>
	<select id="ak_<?php echo $akID; ?>_iconSelect" class='iconSelector' name='<?php echo $this->field('atIconSelectOptionID').'[]';  ?>'>
<?php endif; ?>
    	<?php foreach($options as $opt): 
    		$selected = in_array($opt->getIconSelectAttributeOptionID(), $selectedOptions);
			$selected = $selected?"selected":"";
			$image = \File::getByID($opt->getIconSelectAttributeOptionImageID());
			$path = outputThumbnail($image,'file_manager_listing');
    	?>
	    	<option value='<?php echo $opt->getIconSelectAttributeOptionID(); ?>' data-image='<?php echo $path; ?>' <?php echo $selected; ?>>
	    		<?php echo $opt->getSelectAttributeOptionDisplayValue()?>
	    	</option>
	    <?php endforeach; ?>
    </select>
	<script type="text/javascript">
		$("#ak_<?php echo $akID; ?>_iconSelect").select2({

			formatResult: function (item) {
					var el = $(item.element);
					var imagePath = el.data('image');
					var $item = '<div class="iconItemSelection"><img src='+imagePath+'>'+el.html()+'</div>';
					return $item;
				},
			formatSelection: function (item) {
					var el = $(item.element);
					var imagePath = el.data('image');
					var $item = '<div class="iconItemSelection"><img src='+imagePath+'>'+el.html()+'</div>';
					return $item;
				}

		});
	</script>
