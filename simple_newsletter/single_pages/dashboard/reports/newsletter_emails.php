<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>

<?php 
    $form = \Core::make('helper/form');
    $ih = Loader::helper('concrete/ui');
    $delConfirmJS = t('Are you sure you want to remove this email?');
?>
<script type="text/javascript">
    deleteEmail = function(id) {
        if (confirm('<?php echo $delConfirmJS?>')) { 
            location.href = "<?php echo View::url('/dashboard/reports/newsletter_emails', 'delete')?>/"+id;              
        }
    }
    deleteAllEmails = function() {
        if (confirm('<?php echo $delConfirmJS?>')) { 
            location.href = "<?php echo $this->url('/dashboard/reports/newsletter_emails', 'deleteAll')?>";              
        }
    }
    </script>

<div class="ccm-dashboard-header-buttons">  
    <?php  print $ih->buttonJs(t('Delete All'), "deleteAllEmails()", 'right', 'btn btn-danger');?> 
    
</div>
<form method="post" action="<?php  echo $this->action('download_list'); ?>">
        <?php  echo $form->submit('download', 'Download Full List (.csv)',['class'=>'btn btn-danger']); ?>
</form>
<hr>
<form method="post" action="<?php  echo $this->action('download_confirmed'); ?>">
    <?php  echo $form->submit('download', 'Download Confirmed List (.csv)',['class'=>'btn btn-danger']); ?>
</form>
<hr>
<div class="ccm-dashboard-content-full">

        
        <div class="table-responsive">
        

            <table class="ccm-search-results-table">
                <thead>
                    <tr>
                        <th><span>Email</span></th>
    					<th><span>IP Address</span></th>
    					<th><span>Created</span></th>
    					<th><span>Confirmed</span></th>
                        <th><span>Unsubscribed</span></th>
                        <th><span>Actions</span></th>
                    </tr>
                </thead>
                <?php  foreach ($subscriptions as $subscription): 
                    
                ?>
					<tr>
						<td><?php  echo htmlentities($subscription->getEmail()); ?></td>
						<td><?php  echo $subscription->getIP(); ?></td>
						<td><?php  echo $subscription->getDateCreated(); ?></td>
						<td><?php  echo $subscription->getDateConfirmed(); ?></td>
                        <td><?php  echo $subscription->getUnsubscribed()?'Yes':'No'; ?></td>
                        <td>
                            <?php //echo $subscription->generateUnsubscribeUrl(); ?>
                            <?php  print $ih->buttonJs(t('Delete'), "deleteEmail({$subscription->getID()})", 'left', 'btn btn-danger');?> </td>

					</tr>
				<?php  endforeach; ?>
			</table>
   	</div>

    <div class="ccm-search-results-pagination">
<?php echo $paginator->renderDefaultView(); ?>
    </div>

</div> 
