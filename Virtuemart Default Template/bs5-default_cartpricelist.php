<?php

/**
 * Layout for the shopping cart
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link https://virtuemart.net
 * @copyright Copyright (c) 2004 - 2016 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
 */

// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
?>

<fieldset class="vm-fieldset-pricelist">
	<div class="vm-checkout-products">
		<?php foreach ($this->cart->products as $pkey => $prow) : ?>
			<?php $prow->prices = array_merge($prow->prices,$this->cart->cartPrices[$pkey]); ?>
			<div class="row align-items-center mb-3">
				<div class="col-3 col-md-2 mb-1 mb-md-0">
		 			<?php if ($prow->virtuemart_media_id) : ?>
						<div class="cart-images">
							<?php echo !empty($prow->images[0]) ? $prow->images[0]->displayMediaThumb ('class="img-fluid img-thumbnail"', FALSE) : ''; ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="col-md-10">
					<div class="row align-items-end">
						<div class="col-5 col-md-6">
							<div class="vm-cart-item-name"><?php echo HTMLHelper::link ($prow->url, $prow->product_name); ?></div>
						</div>
						<div class="col-4 col-md-3">
							<div class="vm-cart-item-quantity">
								<?php
								if ($prow->step_order_level)
									$step=$prow->step_order_level;
								else
									$step=1;
								if($step==0)
									$step=1;
								?>
								<div class="input-group input-group-sm input-group input-group-sm justify-content-end flex-nowrap">
									<button class="btn btn-link vm-cart-item-quantity_minus" type="button">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-dash" viewBox="0 0 16 16">
											<path d="M4 8a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8"/>
										</svg>
									</button>
									<input type="text"
										onblur="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
										onclick="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
										onchange="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
										onsubmit="Virtuemart.checkQuantity(this,<?php echo $step?>,'<?php echo vmText::_ ('COM_VIRTUEMART_WRONG_AMOUNT_ADDED',true)?>');"
										title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" class="quantity-input js-recalculate form-control p-1" size="3" maxlength="4" name="quantity[<?php echo $pkey; ?>]" value="<?php echo $prow->quantity ?>" data-step="<?php echo $step?>" />
									<button class="btn btn-link vm-cart-item-quantity_plus" type="button">
										<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16">
											<path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4"/>
										</svg>
									</button>
								</div>
								<button type="submit" class="vm2-add_quantity_cart d-none" name="updatecart.<?php echo $pkey ?>" title="<?php echo  vmText::_ ('COM_VIRTUEMART_CART_UPDATE') ?>" data-dynamic-update="1" ></button>
							</div>
						</div>
						<div class="col-3">
					 		<div class="vm-cart-item-basicprice text-end">
								<?php
								if (VmConfig::get ('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) {
									echo '<del>' . $this->currencyDisplay->createPriceDiv ('basePriceWithTax', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</del><br />';
								} elseif (VmConfig::get ('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && !empty($prow->prices['basePriceVariant']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) {
									echo '<del>' . $this->currencyDisplay->createPriceDiv ('basePriceVariant', '', $prow->prices, TRUE, FALSE, $prow->quantity) . '</del><br />';
								}

								echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $prow->prices, FALSE, FALSE, $prow->quantity);
								?>
							</div>
						</div>
					</div>
					<div class="my-2 border-bottom"></div>
					<div class="row">
						<div class="col-6">
							<div class="vm-cart-item-sku small"><?php echo vmText::_('COM_VIRTUEMART_CART_SKU') . ' : ' . $prow->product_sku; ?></div>
							<div class="vm-cart-item-cfields small"><?php echo $this->customfieldsModel->CustomsFieldCartDisplay($prow); ?></div>
						</div>
						<div class="col-6 text-end">
							<button class="vm2-remove_from_cart" type="submit" name="delete.<?php echo $pkey ?>"><?php echo vmText::_ ('COM_VIRTUEMART_CART_DELETE') ?></button>
						</div>
					</div>
				</div>
				<input type="hidden" name="cartpos[]" value="<?php echo $pkey ?>">
			</div>
		<?php endforeach; ?>
	</div>

	<?php if (VmConfig::get ('coupons_enable')) : ?>
		<div class="vm-checkout-coupon mt-4 mb-5">
			<?php if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) : ?>
				<?php echo $this->loadTemplate('coupon'); ?>
			<?php endif; ?>

			<?php if (!empty($this->cart->cartData['couponCode'])) : ?>
				<span class="h4 badge text-bg-success"><?php echo $this->cart->cartData['couponCode']; ?> <?php echo $this->cart->cartData['couponDescr'] ? ('(-' . $this->cart->cartData['couponDescr'] . ')') : ''; ?></span>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="vm-checkout-subtotals p-3 mb-4 bg-light">
		<?php if ($this->cart->cartPrices['discountAmount']) : ?>
			<div class="row mb-2 small">
				<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?></div>
				<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ('discountAmount', '', $this->cart->cartPrices, FALSE); ?></div>
			</div>
		<?php endif; ?>

		<div class="row mb-2">
			<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL'); ?></div>
			<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ('salesPrice', '', $this->cart->cartPrices, FALSE) ?></div>
		</div>

		<?php if (VmConfig::get ('coupons_enable') && !empty($this->cart->cartPrices['salesPriceCoupon'])) : ?>
			<div class="row mb-2">
				<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_COUPON_DISCOUNT'); ?></div>
				<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], FALSE); ?></div>
			</div>
		<?php endif; ?>

		<div class="row mb-2">
			<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_SHIPPING'); ?></div>
			<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], FALSE); ?></div>
		</div>

		<div class="row mb-2">
			<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_PAYMENT'); ?></div>
			<div class="col text-end"><?php  echo $this->currencyDisplay->createPriceDiv ('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], FALSE); ?></div>
		</div>

		<?php foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule) : ?>
			<div class="row mb-2">
				<div class="col"><?php echo vmText::_($rule['calc_name']) ?></div>
				<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?></div>
			</div>
		<?php endforeach; ?>

		<?php foreach ($this->cart->cartData['taxRulesBill'] as $rule) : ?>
			<?php if ($rule['calc_value_mathop']=='avalara') continue; ?>
			<div class="row mb-2">
				<div class="col"><?php echo vmText::_($rule['calc_name']) ?></div>
				<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?></div>
			</div>
		<?php endforeach; ?>

		<?php foreach ($this->cart->cartData['DATaxRulesBill'] as $rule) : ?>
			<div class="row mb-2">
				<div class="col"><?php echo vmText::_($rule['calc_name']) ?></div>
				<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv ($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], FALSE); ?></div>
			</div>
		<?php endforeach; ?>

		<div class="border-top mb-2"></div>

		<div class="row">
			<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL'); ?></div>
			<div class="col fw-bold text-end"><?php echo $this->currencyDisplay->createPriceDiv ('billTotal', '', $this->cart->cartPrices['billTotal'], FALSE); ?></div>
		</div>

		<?php if ($this->totalInPaymentCurrency) : ?>
			<div class="row my-2">
				<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_TOTAL_PAYMENT') ?></div>
				<div class="col text-end"><?php echo $this->totalInPaymentCurrency; ?></div>
			</div>
		<?php endif; ?>

		<?php if (VmConfig::get ('show_tax')) : ?>
			<div class="row my-2 small">
				<div class="col"><?php echo vmText::_ ('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT'); ?></div>
				<div class="col text-end"><?php  echo $this->currencyDisplay->createPriceDiv ('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], FALSE); ?></div>
			</div>
		<?php endif; ?>

		<?php if (!empty($this->cart->cartData) && !empty($this->cart->cartData['VatTax'])) : ?>
			<?php $c = count($this->cart->cartData['VatTax']); ?>
			<?php if (!VmConfig::get ('show_tax') or $c>1) : ?>
				<?php if ($c > 0 ) : ?>
					<h2 class="h5 fw-normal"><?php echo vmText::_ ('COM_VIRTUEMART_TOTAL_INCL_TAX') ?></h2>
					<?php foreach ($this->cart->cartData['VatTax'] as $vatTax) : ?>
						<?php if (!empty($vatTax['result'])) : ?>
							<div class="row mb-1">
								<div class="col"><?php echo shopFunctionsF::getTaxNameWithValue(vmText::_($vatTax['calc_name']),$vatTax['calc_value']) ?></div>
								<div class="col text-end"><?php echo $this->currencyDisplay->createPriceDiv( 'taxAmount', '', $vatTax['result'], FALSE, false, 1.0,false,true ) ?></div>
							</div>
						<?php endif; ?>
					<?php endforeach; ?>
				<?php endif; ?>
			<?php endif; ?>
		<?php endif; ?>
	</div>
</fieldset>
<?php
vmJsApi::addJScript('vm-checkout-qty','
jQuery(function($) {
	$(\'.vm-cart-item-quantity .btn\').click(function(){
		let qty = parseInt($(this).siblings(\'input\').val());
		let step = parseInt($(this).siblings(\'input\').attr(\'data-step\'));

		if ($(this).hasClass(\'vm-cart-item-quantity_plus\')) {
			$(this).siblings(\'input\').val(qty + step).change();
			$(this).parent().next(\'button\').click();
		} else {
			if (qty > step) {
				$(this).siblings(\'input\').val(qty - step).change();
				$(this).parent().next(\'button\').click();
			}
		}

	});
});
');
?>