<?php   
	defined('C5_EXECUTE') or die("Access Denied.");
	$form = Loader::helper('form'); 
?>


<fieldset>
	<legend><?php  echo t("Attribute Options"); ?></legend>

	<div class="form-group">

		<div class="checkbox">
			<label>
				<?php   echo $form->checkbox("preserveOrder",1,$preserveOrder); ?>
				<?php  echo t("Perserve Order of Values"); ?>
			</label>
		</div>

		<div class="checkbox">
			<label>
			<?php   echo $form->checkbox("uniqueValues",1,$uniqueValues); ?>
			<?php  echo t("Unique Values"); ?>
			</label>
		</div>

		<?php  echo $form->label("single",t("Selection Type")); ?>
		<?php  echo $form->select("single",[1=>'Single',0=>'Multi'],$single) ?>
	</div>

	<?php   
		$handle = Loader::helper("text")->camelcase($akHandle);
		$filterName = "filter".$handle;
		if($handle):
	?>
		<h4><?php  echo t("Name of the filter function: ").$filterName; ?></h4>	
		<div><?php  echo t('Example'); ?>:</div>
		<code style='display:block;'>
			<span style="color: #FF9400">
				<span style="color: #2956DB">public&nbsp;static function&nbsp;</span>
				<span style="color: #489D45"><?php  echo $filterName; ?></span><span style="color: #808080">(</span>
				<span style="color: #000000">&amp;$srchController</span><span style="color: #808080">,</span>
				<span style="color: #000000">&amp;$fileList</span><span style="color: #808080">){<br></span>
				<span style="color: #0080FF">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color: #000000">$srchController-&gt;searchBar&nbsp;</span>
				<span style="color: #808080">=&nbsp;</span><span style="color: #000000">false</span>
				<span style="color: #808080">;<br></span>
				<span style="color: #0080FF">&nbsp;&nbsp;&nbsp;&nbsp;</span>
				<span style="color: #C0C0C0">//&nbsp;$fileList-&gt;setItemsPerPage(1);<br></span>
				<span style="color: #808080">}<br></span>
			</span>
		</code>
		
		
	<?php  endif; ?>
</fieldset>
