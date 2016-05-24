<fieldset class="ccm-attribute ccm-attribute-select">
<legend><?php echo t('Select Options')?></legend>

<div class="form-group">
    <label><?php echo t("Multiple Values")?></label>
    <div class="checkbox">
        <label>
            <?php echo $form->checkbox('akAllowMultipleValues', 1, $akAllowMultipleValues)?> <span><?php echo t('Allow multiple options to be chosen.')?></span>
        </label>
    </div>
</div>
</fieldset>

<div class="clearfix">
<label><?php echo t('Values')?></label>

<div class='icon_selector_values'>
<?php
Loader::helper('text');
foreach($akSelectValues as $v) { 
	if ($v->getSelectAttributeOptionTemporaryID() != false) {
		$akSelectValueID = $v->getSelectAttributeOptionTemporaryID();
	} else {
		$akSelectValueID = $v->getIconSelectAttributeOptionID();
	}
	?>
	<div class='atIconOptionWrapper' id='valueWrapper_<?php echo $akSelectValueID; ?>'>
	<div class='viewContainer'>
		<div class="rightCol">
			<button class="btn btn-primary" type="button" onClick="IconSelectHelpers.editValue('<?php echo $akSelectValueID; ?>')" ><?php echo t('Edit')?></button>
			<button class="btn btn-danger" type="button" onClick="IconSelectHelpers.deleteValue('<?php echo $akSelectValueID; ?>')" ><?php echo t('Delete')?></button>
		</div>	
		<div class="leftCol">
			<span class='optionValueView' data-oldval='<?php echo $v->getSelectAttributeOptionDisplayValue(); ?>'><?php echo $v->getSelectAttributeOptionDisplayValue(); ?></span>
		</div>
	</div>

	<div class='editContainer' style="display:none">
		<div class="leftCol">
			<input type='text' class='editable value_<?php echo $akSelectValueID; ?>' name="option[<?php echo $akSelectValueID; ?>][optValue]" value='<?php echo $v->getSelectAttributeOptionDisplayValue(); ?>'>
			<input type='hidden'  name="option[<?php echo $akSelectValueID; ?>][new]" value='0'>
			<?php 
				$al = Core::make('helper/concrete/asset_library');
		        $form = '<div class="ccm-attribute ccm-attribute-image-file">';
		        $form .= $al->image('optionFile_'.$akSelectValueID,'option['.$akSelectValueID.'][optIcon]', t('Choose File'),File::getByID((int)$v->getIconSelectAttributeOptionImageID()));
		        $form .= '</div>';
		        print $form;
			?>
			
		</div>
		<div class="rightCol">
			<input class="btn btn-default" type="button" onClick="IconSelectHelpers.cancelEdit('<?php echo $akSelectValueID; ?>')" value="<?php echo t('Cancel')?>" />
			<input class="btn btn-success" type="button" onClick="IconSelectHelpers.changeValue('<?php echo $akSelectValueID; ?>')" value="<?php echo t('Save')?>" />
		</div>	
	</div>

</div>
<?php } ?>

</div>

</div>

<div class="clearfix">
<input class="btn btn-primary" type="button" onClick="IconSelectHelpers.addValue()" value="<?php echo t('Add') ?>" />

<script type="text/template" id="icon_select_template">
<div class='atIconOptionWrapper' id='valueWrapper_<%- id %>'>
	<div class='viewContainer'>
		<div class="rightCol">
			<button class="btn btn-primary" type="button" onClick="IconSelectHelpers.editValue('<%- id %>')" ><?php echo t('Edit')?></button>
			<button class="btn btn-danger" type="button" onClick="IconSelectHelpers.deleteValue('<%- id %>')" ><?php echo t('Delete')?></button>
		</div>	
		<div class="leftCol">
			<span class='optionValueView' data-oldval='<%- name %>'><%- name %></span>
		</div>
	</div>

	<div class='editContainer' style="display:none">
		<div class="leftCol">
			<input type='text' class='editable value_<%- id %>' name="option[<%- id %>][optValue]" value='<%- name %>'>
			<input type='hidden'  name="option[<%- id %>][new]" value='1'>
			<div class="ccm-attribute ccm-attribute-image-file">
				<div class="ccm-file-selector" data-file-selector="optionFile_<%- id %>"></div>
			</div>
		</div>
		<div class="rightCol">
			<input class="btn btn-default" type="button" onClick="IconSelectHelpers.cancelEdit('<%- id %>')" value="<?php echo t('Cancel')?>" />
			<input class="btn btn-success" type="button" onClick="IconSelectHelpers.changeValue('<%- id %>')" value="<?php echo t('Save')?>" />
		</div>	
	</div>

</div>
</script>
