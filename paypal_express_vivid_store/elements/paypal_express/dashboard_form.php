<?php  
defined('C5_EXECUTE') or die(_("Access Denied.")); 
extract($vars);
?>
<div class="form-group">
    <label><?php echo t('PaypalExpress Username')?></label>
    <?php  echo $form->text('paypalExpressUsername', $paypalExpressUsername); ?>
</div>

<div class="form-group">
    <label><?php echo t('PaypalExpress Password')?></label>
    <?php  echo $form->text('paypalExpressPassword', $paypalExpressPassword); ?>
</div>

<div class="form-group">
    <label><?php echo t('PaypalExpress Signature')?></label>
    <?php  echo $form->text('paypalExpressSignature', $paypalExpressSignature); ?>
</div>

<div class="form-group">
    <label><?php echo t('PaypalExpress Logo Path')?></label>
    <?php  echo $form->text('paypalExpressLogoPath', $paypalExpressLogoPath); ?>
</div>

<div class="form-group">
    <label><?php echo t('Currency Code')?></label>
	<?php  echo $form->select('paypalExpressCurrencyCode', $paypal_currency_codes, $paypalExpressCurrencyCode, array('style' => 'width: 100%'));?>
</div>

<div class="form-group">
	<label><?php echo t('Mode')?></label>
	<div class="input">
		<?php  echo $form->select('paypalExpressMode',array('test'=>'Sandbox','live'=>'Live'),$paypalExpressMode); ?>
	</div>
</div>

<div class="form-group">
	<label><?php echo t('Transaction Type')?></label>
	<div class="input">
		<?php  echo $form->select('paypalExpressTransactionType',array('Authorization'=>'Authorization','Sale'=>'Sale'),$paypalExpressTransactionType); ?>
	</div>
</div>
