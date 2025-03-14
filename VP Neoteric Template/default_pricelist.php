<?php

/**
 * @package     VP Neoteric
 *
 * @author      Abhishek Das <info@virtueplanet.com>
 * @link        https://www.virtueplanet.com
 * @copyright   Copyright (C) 2023-2025 Virtueplanet Services LLP. All Rights Reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

defined('_JEXEC') or die;

$taxColumnName = vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT');
$vatTaxCount   = 0;

if (!empty($this->cart->cartData['VatTax'])) {
    $vatTaxCount = count($this->cart->cartData['VatTax']);

    if ($vatTaxCount < 2) {
        reset($this->cart->cartData['VatTax']);

        $taxd          = current($this->cart->cartData['VatTax']);
        $taxColumnName = shopFunctionsF::getTaxNameWithValue(vmText::_($taxd['calc_name']), $taxd['calc_value']);
    }
}

$columnCountTillQty = 3;
$totalColumnCount   = 5;

if (VmConfig::get('show_tax')) {
    $totalColumnCount++;
}

$qtyErrorMessage = vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED', true);
?>
<table class="table table-cart cart-summary">
    <thead>
        <tr>
            <th class="cart-item-name align-middle">
                <?php echo vmText::_('COM_VIRTUEMART_CART_NAME'); ?>
            </th>
            <th class="cart-item-basicprice align-middle text-end">
                <?php echo vmText::_('COM_VIRTUEMART_CART_PRICE'); ?>
            </th>
            <th class="cart-item-quantity align-middle text-center">
                <?php echo vmText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>
            </th>
            <?php if (VmConfig::get('show_tax')) : ?>
                <th class="cart-item-tax align-middle text-end">
                    <?php echo $taxColumnName; ?>
                </th>
            <?php endif; ?>
            <th class="cart-item-discount align-middle text-end">
                <?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>
            </th>
            <th class="cart-item-total align-middle text-end">
                <?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($this->cart->products as $pkey => $prow) : ?>
            <?php $prow->prices = array_merge($prow->prices, $this->cart->cartPrices[$pkey]); ?>
            <tr class="cart-item-wrapper pkey-<?php echo $pkey; ?><?php !empty($prow->class) ? ' ' . $prow->class : ''; ?>">
                <td class="cart-item-name align-top">
                    <div class="cart-item-info-section d-flex gap-3">
                        <?php if (VmConfig::get('oncheckout_show_images')) : ?>
                            <div class="cart-item-image align-top">
                                <?php if (!empty($prow->virtuemart_media_id) && !empty($prow->images[0])) : ?>
                                    <?php echo HTMLHelper::link($prow->url, $prow->images[0]->displayMediaThumb(['class' => 'cart-thumbnail-image'], false)); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <div class="cart-item-info">
                            <h6>
                                <?php echo HTMLHelper::link($prow->url, $prow->product_name, ['class' => 'link-body']); ?>
                            </h6>
                            <?php echo $this->customfieldsModel->CustomsFieldCartDisplay($prow); ?>
                            <div class="cart-item-sku">
                                <?php echo vmText::_('COM_VIRTUEMART_CART_SKU'); ?>:&nbsp;<span class="text-muted"><?php echo $prow->product_sku; ?></span>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="cart-item-basicprice align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_PRICE'); ?>">
                    <?php if (VmConfig::get('checkout_show_origprice', 1) && $prow->prices['discountedPriceWithoutTax'] != $prow->prices['priceWithoutTax']) : ?>
                        <?php echo '<s class="product-price-before-discount">' . $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false) . '</s>'; ?>
                    <?php endif; ?>
                    <?php if ($prow->prices['discountedPriceWithoutTax']) : ?>
                        <?php echo $this->currencyDisplay->createPriceDiv('discountedPriceWithoutTax', '', $prow->prices, false, false, 1.0, false, true); ?>
                    <?php else : ?>
                        <?php echo $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, false, false, 1.0, false, true); ?>
                    <?php endif; ?>
                </td>
                <td class="cart-item-quantity align-top text-center" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_QUANTITY'); ?>">
                    <?php $step = !empty($prow->step_order_level) ? $prow->step_order_level : 1; ?>
                    <?php $init = $prow->min_order_level > 1 ? $prow->min_order_level : $step; ?>
                    <?php $init = $init < $step ? $step : $init; ?>
                    <?php $init = $init > $step ? ceil($init / $step) * $step : $init; ?>
                    <?php $maxOrder = !empty($prow->max_order_level) ? ' max="' . $prow->max_order_level . '"' : ''; ?>
                    <?php $qtyInputJSEvents = [
                        'onblur'   => "Virtuemart.checkQuantity(this, " . $step . ",'" . $qtyErrorMessage . "');",
                        'onclick'  => "Virtuemart.checkQuantity(this, " . $step . ",'" . $qtyErrorMessage . "');",
                        'onchange' => "Virtuemart.checkQuantity(this, " . $step . ",'" . $qtyErrorMessage . "');",
                        'onsubmit' => "Virtuemart.checkQuantity(this, " . $step . ",'" . $qtyErrorMessage . "');"
                    ]; ?>
                    <?php $qtyInputJSEvents = ArrayHelper::toString($qtyInputJSEvents); ?>
                    <div class="quanity-update-section d-flex justify-content-center align-items-center gap-1">
                        <input type="number" class="form-control quantity-input js-recalculate" name="quantity[<?php echo $pkey; ?>]" data-errStr="<?php echo vmText::_('COM_VIRTUEMART_WRONG_AMOUNT_ADDED') ?>" value="<?php echo $prow->quantity; ?>" min="<?php echo $init; ?>" step="<?php echo $step; ?>" <?php echo $maxOrder; ?> <?php echo $qtyInputJSEvents; ?> />
                        <button type="submit" class="btn btn-link px-1 py-2 border-0" name="updatecart.<?php echo $pkey ?>" title="<?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?>" data-dynamic-update="1">
                            <i class="fas fa-sync-alt fa-lg" aria-hidden="true"></i>
                            <span class="visually-hidden"><?php echo  vmText::_('COM_VIRTUEMART_CART_UPDATE') ?></span>
                        </button>
                        <button type="submit" class="btn btn-link px-1 py-2 border-0" name="delete.<?php echo $pkey; ?>" title="<?php echo vmText::_('COM_VIRTUEMART_CART_DELETE'); ?>">
                            <i class="fas fa-trash fa-lg" aria-hidden="true"></i>
                            <span class="visually-hidden"><?php echo vmText::_('COM_VIRTUEMART_CART_DELETE'); ?></span>
                        </button>
                    </div>
                    <input type="hidden" name="cartpos[]" value="<?php echo $pkey; ?>" />
                </td>
                <?php if (VmConfig::get('show_tax')) : ?>
                    <?php if ($prow->prices['taxAmount']) : ?>
                        <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $prow->prices, false, false, $prow->quantity, false, true); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($prow->prices['discountAmount']) : ?>
                    <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                        <?php echo $this->currencyDisplay->createPriceDiv('discountAmount', '', $prow->prices, false, false, $prow->quantity, false, true); ?>
                    </td>
                <?php else : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
                <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                    <div class="cart-item-total-prices">
                        <?php if (VmConfig::get('checkout_show_origprice', 1) && !empty($prow->prices['basePriceWithTax']) && $prow->prices['basePriceWithTax'] != $prow->prices['salesPrice']) : ?>
                            <?php echo '<s class="product-price-before-discount">' . $this->currencyDisplay->createPriceDiv('basePriceWithTax', '', $prow->prices, true, false, $prow->quantity) . '</s>'; ?>
                        <?php elseif (VmConfig::get('checkout_show_origprice', 1) && empty($prow->prices['basePriceWithTax']) && !empty($prow->prices['basePriceVariant']) && $prow->prices['basePriceVariant'] != $prow->prices['salesPrice']) : ?>
                            <?php echo '<s class="product-price-before-discount">' . $this->currencyDisplay->createPriceDiv('basePriceVariant', '', $prow->prices, true, false, $prow->quantity) . '</s>'; ?>
                        <?php endif; ?>
                        <?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $prow->prices, false, false, $prow->quantity); ?>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                <div class="section-title">
                    <?php echo vmText::_('COM_VIRTUEMART_ORDER_PRINT_PRODUCT_PRICES_TOTAL'); ?>
                </div>
            </td>
            <?php if (VmConfig::get('show_tax')) : ?>
                <?php if (!empty($this->cart->cartPrices['taxAmount'])) : ?>
                    <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                        <?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $this->cart->cartPrices, false, false, true); ?>
                    </td>
                <?php else : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (!empty($this->cart->cartPrices['discountAmount'])) : ?>
                <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv('discountAmount', '', $this->cart->cartPrices, false); ?>
                </td>
            <?php else : ?>
                <td class="cart-column-empty"></td>
            <?php endif; ?>
            <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                <?php echo $this->currencyDisplay->createPriceDiv('salesPrice', '', $this->cart->cartPrices, false); ?>
            </td>
        </tr>
        <?php if (VmConfig::get('coupons_enable')) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <?php if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) : ?>
                        <?php echo $this->loadTemplate('coupon'); ?>
                    <?php endif; ?>
                    <?php if (!empty($this->cart->cartData['couponCode'])) : ?>
                        <div class="cart-coupon-details">
                            <?php echo $this->cart->cartData['couponCode']; ?>
                            <?php echo $this->cart->cartData['couponDescr'] ? (' (' . $this->cart->cartData['couponDescr'] . ')') : ''; ?>
                        </div>
                    <?php endif; ?>
                </td>
                <?php if (!empty($this->cart->cartData['couponCode'])) : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('couponTax', '', $this->cart->cartPrices['couponTax'], false); ?>
                        </td>
                    <?php endif; ?>
                    <td class="cart-column-empty"></td>
                    <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                        <?php echo $this->currencyDisplay->createPriceDiv('salesPriceCoupon', '', $this->cart->cartPrices['salesPriceCoupon'], false); ?>
                    </td>
                <?php else : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                    <td class="cart-column-empty"></td>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
        <?php foreach ($this->cart->cartData['DBTaxRulesBill'] as $rule) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <div class="section-title">
                        <?php echo vmText::_($rule['calc_name']); ?>
                    </div>
                </td>
                <?php if (VmConfig::get('show_tax')) : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
                <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                </td>
                <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($this->cart->cartData['taxRulesBill'] as $rule) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <div class="section-title">
                        <?php echo vmText::_($rule['calc_name']); ?>
                    </div>
                </td>
                <?php if (VmConfig::get('show_tax')) : ?>
                    <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT'); ?>">
                        <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                    </td>

                <?php endif; ?>
                <td class="cart-column-empty"></td>
                <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($this->cart->cartData['DATaxRulesBill'] as $rule) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <div class="section-title">
                        <?php echo vmText::_($rule['calc_name']); ?>
                    </div>
                </td>
                <?php if (VmConfig::get('show_tax')) : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
                <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                </td>
                <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv($rule['virtuemart_calc_id'] . 'Diff', '', $this->cart->cartPrices[$rule['virtuemart_calc_id'] . 'Diff'], false); ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (VmConfig::get('oncheckout_opc', true) || !VmConfig::get('oncheckout_show_steps', false) || (!VmConfig::get('oncheckout_opc', true) && VmConfig::get('oncheckout_show_steps', false) && !empty($this->cart->virtuemart_shipmentmethod_id))) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <?php if (!$this->cart->automaticSelectedShipment) : ?>
                        <h6 class="mb-2"><?php echo vmText::_('COM_VIRTUEMART_CART_SELECTED_SHIPMENT'); ?></h6>
                        <div class="cart-shipment-name mb-3">
                            <?php echo $this->cart->cartData['shipmentName']; ?>
                        </div>
                        <?php if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) : ?>
                            <?php if (VmConfig::get('oncheckout_opc', 0)) : ?>
                                <?php $previouslayout = $this->setLayout('select'); ?>
                                <?php echo $this->loadTemplate('shipment'); ?>
                                <?php $this->setLayout($previouslayout); ?>
                            <?php else : ?>
                                <?php echo HTMLHelper::link(Route::_('index.php?option=com_virtuemart&view=cart&task=edit_shipment', $this->useXHTML, $this->useSSL), $this->select_shipment_text, ['class' => 'btn btn-secondary mt-3']); ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php echo vmText::_('COM_VIRTUEMART_CART_SHIPPING'); ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php echo $this->cart->cartData['shipmentName']; ?>
                        <?php if (!empty($this->cart->cartPrices['shipmentValue'])) : ?>
                            <span class="me-2">
                                <?php echo $this->currencyDisplay->createPriceDiv('shipmentValue', '', $this->cart->cartPrices['shipmentValue'], false); ?>
                            </span>
                        <?php endif; ?>
                    <?php endif; ?>
                </td>
                <?php if ($this->cart->virtuemart_shipmentmethod_id) : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <?php if ($this->cart->cartPrices['shipmentTax']) : ?>
                            <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                                <?php echo $this->currencyDisplay->createPriceDiv('shipmentTax', '', $this->cart->cartPrices['shipmentTax'], false); ?>
                            </td>
                        <?php else : ?>
                            <td class="cart-column-empty"></td>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($this->cart->cartPrices['salesPriceShipment'] < 0) : ?>
                        <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                    <?php if ($this->cart->cartPrices['salesPriceShipment']) : ?>
                        <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('salesPriceShipment', '', $this->cart->cartPrices['salesPriceShipment'], false); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                    <td class="cart-column-empty"></td>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
        <?php if (VmConfig::get('oncheckout_opc', true) || !VmConfig::get('oncheckout_show_steps', false) || (!VmConfig::get('oncheckout_opc', true) && VmConfig::get('oncheckout_show_steps', false) && !empty($this->cart->virtuemart_paymentmethod_id))) : ?>
            <tr>
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <?php if (!$this->cart->automaticSelectedPayment) : ?>
                        <h6 class="mb-2"><?php echo vmText::_('COM_VIRTUEMART_CART_SELECTED_PAYMENT'); ?></h6>
                        <div class="cart-payment-name mb-3">
                            <?php echo $this->cart->cartData['paymentName']; ?>
                        </div>
                        <?php if (!empty($this->layoutName) && $this->layoutName == $this->cart->layout) : ?>
                            <?php if (VmConfig::get('oncheckout_opc', 0)) : ?>
                                <?php $previouslayout = $this->setLayout('select'); ?>
                                <?php echo $this->loadTemplate('payment'); ?>
                                <?php $this->setLayout($previouslayout); ?>
                            <?php else : ?>
                                <?php echo HTMLHelper::link(Route::_('index.php?option=com_virtuemart&view=cart&task=edit_payment', $this->useXHTML, $this->useSSL), $this->select_payment_text, ['class' => 'btn btn-secondary mt-3']); ?>
                            <?php endif; ?>
                        <?php else : ?>
                            <?php echo vmText::_('COM_VIRTUEMART_CART_PAYMENT'); ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <?php echo $this->cart->cartData['paymentName']; ?>
                    <?php endif; ?>
                </td>
                <?php if ($this->cart->virtuemart_paymentmethod_id) : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <?php if ($this->cart->cartPrices['paymentTax']) : ?>
                            <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                                <?php echo $this->currencyDisplay->createPriceDiv('paymentTax', '', $this->cart->cartPrices['paymentTax'], false); ?>
                            </td>
                        <?php else : ?>
                            <td class="cart-column-empty"></td>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($this->cart->cartPrices['salesPricePayment'] < 0) : ?>
                        <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                    <?php if ($this->cart->cartPrices['salesPricePayment']) : ?>
                        <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                            <?php echo $this->currencyDisplay->createPriceDiv('salesPricePayment', '', $this->cart->cartPrices['salesPricePayment'], false); ?>
                        </td>
                    <?php else : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                <?php else : ?>
                    <?php if (VmConfig::get('show_tax')) : ?>
                        <td class="cart-column-empty"></td>
                    <?php endif; ?>
                    <td class="cart-column-empty"></td>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
            </tr>
        <?php endif; ?>
        <tr class="fw-bolder cart-grand-total">
            <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                <?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>
            </td>
            <?php if (VmConfig::get('show_tax')) : ?>
                <?php if ($this->cart->cartPrices['billTaxAmount']) : ?>
                    <td class="cart-item-tax align-top text-end text-nowrap" data-title="<?php echo $taxColumnName; ?>">
                        <?php echo $this->currencyDisplay->createPriceDiv('billTaxAmount', '', $this->cart->cartPrices['billTaxAmount'], false); ?>
                    </td>
                <?php else : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($this->cart->cartPrices['billDiscountAmount']) : ?>
                <td class="cart-item-discount align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_DISCOUNT_AMOUNT'); ?>">
                    <?php echo $this->currencyDisplay->createPriceDiv('billDiscountAmount', '', $this->cart->cartPrices['billDiscountAmount'], false); ?>
                </td>
            <?php else : ?>
                <td class="cart-column-empty"></td>
            <?php endif; ?>
            <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                <?php echo $this->currencyDisplay->createPriceDiv('billTotal', '', $this->cart->cartPrices['billTotal'], false); ?>
            </td>
        </tr>
        <?php if ($this->totalInPaymentCurrency) : ?>
            <tr class="fw-bolder cart-payment-total">
                <td class="cart-section-header align-top" colspan="<?php echo $columnCountTillQty; ?>">
                    <?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL_PAYMENT'); ?>
                </td>
                <?php if (VmConfig::get('show_tax')) : ?>
                    <td class="cart-column-empty"></td>
                <?php endif; ?>
                <td class="cart-column-empty"></td>
                <td class="cart-item-total align-top text-end text-nowrap" data-title="<?php echo vmText::_('COM_VIRTUEMART_CART_TOTAL'); ?>">
                    <?php echo $this->totalInPaymentCurrency; ?>
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php if ((!VmConfig::get('show_tax') && $vatTaxCount) || $vatTaxCount > 1) : ?>
    <div class="row">
        <div class="col-md-6">
            <div class="p-3 p-md-4 border border-3 mb-5 mb-md-2 mt-md-3">
                <h5><?php echo vmText::_('COM_VIRTUEMART_TOTAL_INCL_TAX'); ?></h5>
                <table class="table">
                    <thead>
                        <tr>
                            <th>
                                <?php echo vmText::_('COM_VIRTUEMART_CART_NAME'); ?>
                            </th>
                            <th class="text-end">
                                <?php echo vmText::_('COM_VIRTUEMART_CART_SUBTOTAL_TAX_AMOUNT'); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($this->cart->cartData['VatTax'] as $vatTax) : ?>
                            <tr>
                                <td>
                                    <?php echo shopFunctionsF::getTaxNameWithValue(vmText::_($vatTax['calc_name']), $vatTax['calc_value']); ?>
                                </td>
                                <td class="text-end">
                                    <?php echo $this->currencyDisplay->createPriceDiv('taxAmount', '', $vatTax['result'], false, false, 1.0, false, true); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
