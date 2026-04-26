<?php
/**
 * @var string $transaction_time
 * @var int $sale_id
 * @var float $discount
 * @var array $cart
 * @var float $subtotal
 * @var array $taxes
 * @var float $total
 * @var float $payments_total
 * @var float $amount_change
 * @var array $config
 * @var string $reference_number
 */

$has_customer = isset($customer) && trim((string)$customer) !== '';
$customer_name = $has_customer ? $customer : '';
$reference_number = !empty($reference_number) ? $reference_number : (!empty($invoice_number) ? $invoice_number : $sale_id);
$highlight_font_size = (int)$config['receipt_font_size'] + 2;
$company_phone_font_size = $highlight_font_size + 4;
$company_phone_min_font_size = max((int)$config['receipt_font_size'], 10);
$total_items = 0.0;

foreach ($cart as $item) {
    if ($item['print_option'] == PRINT_YES) {
        $total_items += (float)$item['quantity'];
    }
}

$total_items_text = fmod($total_items, 1.0) === 0.0 ? (string)(int)$total_items : to_quantity_decimals($total_items);
?>

<div id="receipt_wrapper" style="font-size: <?= esc($config['receipt_font_size']) ?>px;">
    <div id="receipt_header" style="margin-bottom: 20px;">
        <?php if ($config['company_logo'] != '') { ?>
            <div id="company_name">
                <img id="image" src="<?= base_url('uploads/' . esc($config['company_logo'], 'url')) ?>" alt="company_logo">
            </div>
        <?php } ?>

        <?php if ($config['receipt_show_company_name']) { ?>
            <div id="company_name"><?= esc($config['company']) ?></div>
        <?php } ?>

        <div id="company_address" style="font-size: <?= esc((string)$highlight_font_size) ?>px;"><?= nl2br(esc($config['address'])) ?></div>
        <div id="company_phone" data-base-font-size="<?= esc((string)$company_phone_font_size) ?>" data-min-font-size="<?= esc((string)$company_phone_min_font_size) ?>" style="font-size: <?= esc((string)$company_phone_font_size) ?>px; line-height: 1.1; text-align: center; white-space: nowrap; width: 58mm; min-width: 58mm; max-width: 58mm; margin: 2px auto 15px; overflow: hidden; text-overflow: clip;"><?= esc(trim((string)$config['phone'])) ?></div>
    </div>

    <div id="receipt_general_info" style="margin-top: 0; padding-top: 10px;">
        <?php if ($has_customer) { ?>
            <div style="display: flex; justify-content: space-between;">
                <span><?= esc(strtoupper(lang('Sales.customer'))) ?></span>
                <span><?= esc($customer_name) ?></span>
            </div>
            <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
        <?php } ?>
        <div style="display: flex; justify-content: space-between;">
            <span><?= esc($reference_number) ?></span>
            <span><?= esc($transaction_time) ?></span>
        </div>
        <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
    </div>

    <div id="receipt_items" style="margin-top: 6px !important;">
        <?php foreach ($cart as $item) {
            if ($item['print_option'] == PRINT_YES) {
                $item_name = trim($item['name'] . ' ' . $item['attribute_values']);
                $item_name = function_exists('mb_strtoupper') ? mb_strtoupper($item_name, 'UTF-8') : strtoupper($item_name);
        ?>
                <div><?= esc($item_name) ?></div>
                <div style="display: flex; justify-content: space-between; padding-left: 12px;">
                    <span><?= to_quantity_decimals($item['quantity']) ?> x <?= to_currency($item['price']) ?></span>
                    <span><?= to_currency($item[($config['receipt_show_total_discount'] ? 'total' : 'discounted_total')]) ?></span>
                </div>
        <?php
            }
        }
        ?>
        <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
        <table style="width: auto; margin-left: auto; border-collapse: separate; border-spacing: 0; text-align: right;">
            <tbody>
                <tr>
                    <td style="padding: 1px 8px 1px 0; white-space: nowrap; line-height: 1.2;"><?= esc('TOTAL') ?></td>
                    <td style="padding: 1px 0 1px 20px; white-space: nowrap; line-height: 1.2;"><?= to_currency($total) ?></td>
                </tr>
                <tr>
                    <td style="padding: 1px 8px 1px 0; white-space: nowrap; line-height: 1.2;"><?= esc('BAYAR') ?></td>
                    <td style="padding: 1px 0 1px 20px; white-space: nowrap; line-height: 1.2;"><?= to_currency($payments_total) ?></td>
                </tr>
                <tr>
                    <td style="padding: 1px 8px 1px 0; white-space: nowrap; line-height: 1.2;"><?= esc('KEMBALI') ?></td>
                    <td style="padding: 1px 0 1px 20px; white-space: nowrap; line-height: 1.2;"><?= to_currency($amount_change) ?></td>
                </tr>
            </tbody>
        </table>
        <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
        <div style="text-align: left;"><?= esc($total_items_text) ?> <?= esc('items') ?></div>
    </div>

    <div id="sale_return_policy" style="text-align: center; margin-top: 8px;">
        <?= nl2br(esc($config['return_policy'])) ?>
    </div>
</div>

<script>
    (function() {
        const fitCompanyPhone = function() {
            const phoneEl = document.getElementById('company_phone');

            if (!phoneEl) {
                return;
            }

            const baseFontSize = parseFloat(phoneEl.getAttribute('data-base-font-size')) || <?= (float)$company_phone_font_size ?>;
            const minFontSize = parseFloat(phoneEl.getAttribute('data-min-font-size')) || <?= (float)$company_phone_min_font_size ?>;

            let currentFontSize = baseFontSize;
            let guard = 0;

            phoneEl.style.fontSize = currentFontSize + 'px';
            phoneEl.style.letterSpacing = '';

            while (phoneEl.scrollWidth > phoneEl.clientWidth && currentFontSize > minFontSize && guard < 80) {
                currentFontSize -= 0.5;
                phoneEl.style.fontSize = currentFontSize + 'px';
                guard++;
            }

            if (phoneEl.scrollWidth > phoneEl.clientWidth) {
                phoneEl.style.letterSpacing = '-0.2px';
            }
        };

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fitCompanyPhone);
        } else {
            fitCompanyPhone();
        }
    })();
</script>
