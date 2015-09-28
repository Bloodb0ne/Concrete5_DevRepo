<?php 

use \Concrete\Core\File\Type\Type as FileType;

 // $c = Page::getCurrentPage();
 $globalHandle = "gallery_{$akID}";
 $saveHandle = $view->field('galleryItems');


 // var_dump($view->field('galleryItems'));
?>

<a style='cursor:pointer;' class="ccm-file-selector" id="<?php echo $globalHandle; ?>_file"><?php echo t("Add Image"); ?></a><br>
<a style='cursor:pointer;' class="link-selector" id="<?php echo $globalHandle; ?>_url"><?php echo t("Add Link"); ?></a>
<hr>
<ul class='galleryContainer item-select-list' id="<?php echo "{$globalHandle}_container"; ?>">

	<?php foreach($items as $item):
		if(is_object($item)){
			$fv = $item->getApprovedVersion();
			$value = $item->getFileID();
			$name = $fv->getTitle();
			$url = $fv->getRelativePath();
		}else{
			$value = $item;
			$name = $item;
			$url = $item;
		}
	?>
	<li class="galleryEntry">
		<div>
		<i class="ccm-item-select-list-sort ui-sortable-handle"></i>
		<input type="checkbox" name="<?php echo $saveHandle; ?>[]" value='<?php echo $value; ?>' checked>
	   	<span>
			<a class="title" href="<?php echo $url; ?>" target="_blank"><?php echo $name; ?></a>	
		</span>
		</div>
	</li> 
	<?php endforeach; ?>

</ul>

 <script type="text/template" id="gallery_template_file">
<li class="galleryEntry">
	<div>
	<i class="ccm-item-select-list-sort ui-sortable-handle"></i>
	<input type="checkbox" name="<%=handle %>[]" value='<%=fID %>' checked>
   	<span>
		<a class="title" href="<%=url %>" target="_blank"><%=title %></a>	
	</span>
	</div>
</li> 

</script>


<script type="text/template" id="gallery_template_video">
<li class="galleryEntry">
	<div>
	<i class="ccm-item-select-list-sort ui-sortable-handle"></i>
	<input type="checkbox" name="<%=handle %>[]" value='<%=url %>' checked>
   	<span>
		<a class="title" href="<%=url %>" target="_blank"><%=url %></a>	
	</span>
	</div>
</li> 

</script>
<script type="text/javascript">
	$(document).ready(function(){
		$("#<?php echo $globalHandle; ?>_file").on('click',function(){
				ConcreteFileManager.launchDialog(function <?php echo $globalHandle; ?>(data){
					if(data.fID){
						ConcreteFileManager.getFileDetails(data.fID, function(r) {
							var file = r.files[0];
							file.handle = "<?php echo $saveHandle; ?>";
							var template = $("#gallery_template_file").html();
							$('#<?php echo "{$globalHandle}_container"; ?>').append(_.template(template,file));
						});
					}
				},{
					filters: [ {field:"type",type:"<?php echo FileType::T_IMAGE; ?>"} ]
				});
		})

		$("#<?php echo $globalHandle; ?>_url").on('click',function(){
			var w = $(window).width()/2;
			$.fn.dialog.open({
		            width: w,
		            height: '50%',
		            href: CCM_DISPATCHER_FILENAME + '/dialog/videolink',
		            modal: true,
		            data: {},
		            title: "Choose URL",
		            onOpen:function(dialog){
		            	ConcreteEvent.unsubscribe('GalleryChooseUrl');
		            	ConcreteEvent.subscribe('GalleryChooseUrl',function(e,data){
		            		var video = {};
		            		video.handle = "<?php echo $saveHandle; ?>";
		            		video.url = data.url;

							var template = $("#gallery_template_video").html();
							$('#<?php echo "{$globalHandle}_container"; ?>').append(_.template(template,video));

		            		jQuery.fn.dialog.closeTop();
		            	});
		            }
		        });
		})


		$('#<?php echo "{$globalHandle}_container"; ?>').sortable();
	})
</script>