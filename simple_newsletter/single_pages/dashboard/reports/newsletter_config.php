<?php  defined('C5_EXECUTE') or die(_("Access Denied.")); ?>
<?php
Loader::helper('concrete/interface');
 
$ih = new ConcreteInterfaceHelper();
$fh = new FormHelper();

$form = Loader::Helper('form');
echo Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(
        t(' Newsletter Configuration '),"", 'span10', false);
?>

<?php 
    $ih = Loader::helper('concrete/interface');
?>
<script type="text/javascript">
$(document).ready(function(){
    $('#fetch-lists').on('click',function(e){
        e.preventDefault();
        console.log('test');
        var api_key = $("#NEWSLETTER_MAILCHIMP_API_KEY").val();
        $.ajax({
            url: "<?php echo $this->action('getLists'); ?>",
            data: { 'key': api_key },
            method: 'POST',
            success:function(data){
                $("#NEWSLETTER_MAILCHIMP_LIST").html(data);
            }
        })
    })
})
    
</script>
<div class="ccm-pane-body">
 	
        <legend>
            <?php echo t('Newsletter Mailchimp integration') ?>
            
			
        </legend>
        <div>
            
            <form method="post" action="<?php  echo $this->action('saveConfig'); ?>">
                <legend>1. Set the Mailchimp API key(  <a href="http://kb.mailchimp.com/accounts/management/about-api-keys">How to get the key</a>    )</legend>
                <?php echo $form->text('NEWSLETTER_MAILCHIMP_API_KEY',$NEWSLETTER_MAILCHIMP_API_KEY,['label'=>'Mailchimp Key']); ?>
                <button id='fetch-lists' type='button' class='btn btn-success'>Set key</button>
                <div class="clearfix"></div>

                <legend>2. Select a mailchimp mailing list</legend>
                <div class='form-group'>
                    <select name='NEWSLETTER_MAILCHIMP_LIST' id='NEWSLETTER_MAILCHIMP_LIST'>
                        <?php if(!$lists): ?>
                            <option value='-1'>Please select a Mailchimp API Key</option>
                        <?php else: ?>
                            <?php echo $lists; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="clearfix"></div>

                <?php  echo $form->submit('download', 'Save Configuration',['class'=>'btn-info']); ?>
            </form>
   	</div>
    <div class='ccm-pane-footer'>
    </div>
 
</form>
 
<?php
echo Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);
?>
