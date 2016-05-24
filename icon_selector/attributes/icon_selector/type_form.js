var IconSelectHelpers = {

	deleteValue:function(id){
		if(confirm(ccmi18n.deleteAttributeValue)) {
			$('#valueWrapper_'+id).remove();				
		}
	},
	addValue:function(){
		var id = 't' + new Date().getTime();
		var rawTemplate = $('#icon_select_template').html();
		var template = _.template(rawTemplate);
		$('.icon_selector_values').append(template({id:id,name:'New Value'}));
		$('[data-file-selector=optionFile_'+id+']').concreteFileSelector({'inputName': 'option['+id+'][optIcon]', 'filters': [] });
	},
	editValue:function(id){
		$('#valueWrapper_'+id+' .viewContainer').fadeOut(function(){
			$('#valueWrapper_'+id+' .editContainer').fadeIn();

		});
	},
	changeValue:function(id){
		var src = $('#valueWrapper_'+id+'.ccm-file-selector-file-selected-thumbnail img').attr('src');
		var newValue = $('.value_'+id).val();
		$('#valueWrapper_'+id+' .optionValueView').html(newValue);

		$('#valueWrapper_'+id+' .editContainer').fadeOut(function(){
			$('#valueWrapper_'+id+' .viewContainer').fadeIn();
		});
	},
	cancelEdit:function(id){
		var oldValue = $('#valueWrapper_'+id+' .optionValueView').data('oldval');
		$('.value_'+id).val(oldValue);
		

		$('#valueWrapper_'+id+' .editContainer').fadeOut(function(){
			$('#valueWrapper_'+id+' .viewContainer').fadeIn();
		});
	}
}