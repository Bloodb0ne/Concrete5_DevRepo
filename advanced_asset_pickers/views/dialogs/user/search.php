<?php  
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div data-search="users" class="ccm-ui">
    <?php   Loader::packageElement('users/search','advanced_asset_pickers', array('controller' => $searchController)) ?>
</div>

<script type="text/javascript">
    $(function () {
        $('div[data-search=users]').customAjaxUserSelector({
            result: <?php   echo $result?>,
            mode: '<?php   echo $searchController->getSelectionMode(); ?>'
        });
    });
</script>
