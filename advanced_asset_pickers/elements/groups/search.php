<?php   defined('C5_EXECUTE') or die("Access Denied."); ?> 
<?php  
$form = Loader::helper('form');
$searchRequest = $_REQUEST;
$result = Loader::helper('json')->encode($controller->getSearchResultObject()->getJSONObject());
$tree = GroupTree::get();
$guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
$registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);


?>

<style type="text/css">
	div[data-search=groups] form.ccm-search-fields {
		margin-left: 0px !important;
	}
</style>

<div data-search="groups">
<script type="text/template" data-template="search-form">
<form role="form" data-search-form="groups" action="<?php   echo URL::to('/advanced_asset_pickers/group_search/submit')?>" class="form-inline ccm-search-fields ccm-search-fields-none">
	<input type="hidden" name="filter" value="<?php   echo h($searchRequest['filter'])?>" />

	<div class="ccm-search-fields-row">
	<div class="form-group">
		<select data-bulk-action="groups" disabled class="ccm-search-bulk-action form-control">
			<option value=""><?php   echo t('Items Selected')?></option>
			<option value="choose-selected"><?php   echo t('Choose Selected')?></option>
		</select>
	</div>
	<div class="form-group">
		<div class="ccm-search-main-lookup-field">
			<i class="fa fa-search"></i>
			<?php   echo $form->search('keywords', $searchRequest['keywords'], array('placeholder' => t('Name')))?>
			<button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?php   echo t('Search')?></button>
		</div>
	</div>
	</div>
</form>
</script>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function(group) { console.log(group); %>
<tr data-group-row="choose">
<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="individual" value="<%=group.gID%>" /></span></td>
	<% for(i = 0; i < group.columns.length; i++) {
		var column = group.columns[i]; 
		%>
		<td><%=column.value%></td>
	<% } %>
</tr>
<% }); %>
</script>


<div data-search-element="wrapper"></div>

<div class="group-tree" data-group-tree="<?php   echo $tree->getTreeID()?>"></div>

<div data-search-element="results">

<table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
<thead>
</thead>
<tbody>
</tbody>
</table>

<div class="ccm-search-results-pagination"></div>

</div>


<script type="text/template" data-template="search-results-pagination">
<%=paginationTemplate%>
</script>

<script type="text/template" data-template="search-results-table-head">
<tr>
	<th><span class="ccm-search-results-checkbox"><input type="checkbox" data-search-checkbox="select-all" class="ccm-flat-checkbox" /></span></th>
	<% 
	for (i = 0; i < columns.length; i++) {
		var column = columns[i];
		if (column.isColumnSortable) { %>
			<th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
		<% } else { %>
			<th><span><%=column.title%></span></th>
		<% } %>
	<% } %>
</tr>
</script>

<script type="text/javascript">
$(function() {
	$('div[data-search-element=results]').show();
	$('div[data-search=groups]').customAjaxGroupSelector({
            result: <?php   echo $result?>,
            mode: '<?php   echo $controller->getSelectionMode(); ?>'
        });

});
</script>

</div>




