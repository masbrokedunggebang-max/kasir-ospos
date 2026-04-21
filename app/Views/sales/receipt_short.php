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
 */

$customer_name = isset($customer) && trim((string)$customer) !== '' ? $customer : 'UMUM';
$reference_number = !empty($invoice_number) ? $invoice_number : $sale_id;
$highlight_font_size = (int)$config['receipt_font_size'] + 2;
$total_items = 0.0;

foreach ($cart as $item) {
    if ($item['print_option'] == PRINT_YES) {
        $total_items += (float)$item['quantity'];
    }
}

$total_items_text = fmod($total_items, 1.0) === 0.0 ? (string)(int)$total_items : to_quantity_decimals($total_items);
?>

<div id="receipt_wrapper" style="font-size: <?= esc($config['receipt_font_size']) ?>px;">
    <div id="receipt_header">
        <?php if ($config['company_logo'] != '') { ?>
            <div id="company_name">
                <img id="image" src="<?= base_url('uploads/' . esc($config['company_logo'], 'url')) ?>" alt="company_logo">
            </div>
        <?php } ?>

        <?php if ($config['receipt_show_company_name']) { ?>
            <div id="company_name"><?= esc($config['company']) ?></div>
        <?php } ?>

        <div id="company_address" style="font-size: <?= esc((string)$highlight_font_size) ?>px;"><?= nl2br(esc($config['address'])) ?></div>
        <div id="company_phone" style="font-size: <?= esc((string)$highlight_font_size) ?>px;"><?= esc($config['phone']) ?></div>
    </div>

    <div id="receipt_general_info" style="margin-top: 10px;">
        <div style="display: flex; justify-content: space-between;">
            <span><?= esc(strtoupper(lang('Sales.customer'))) ?></span>
            <span><?= esc($customer_name) ?></span>
        </div>
        <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
        <div style="display: flex; justify-content: space-between;">
            <span><?= esc($reference_number) ?></span>
            <span><?= esc($transaction_time) ?></span>
        </div>
        <div style="border-top: 1px dashed #000; margin: 6px 0;"></div>
    </div>

    <div id="receipt_items">
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
        <table style="width: 100%; border-collapse: collapse; text-align: right;">
            <tbody>
                <tr>
                    <td style="padding: 1px 10px 1px 0;"><?= esc('TOTAL') ?></td>
                    <td style="padding: 1px 0; white-space: nowrap;"><?= to_currency($total) ?></td>
                </tr>
                <tr>
                    <td style="padding: 1px 10px 1px 0;"><?= esc('BAYAR') ?></td>
                    <td style="padding: 1px 0; white-space: nowrap;"><?= to_currency($payments_total) ?></td>
                </tr>
                <tr>
                    <td style="padding: 1px 10px 1px 0;"><?= esc('KEMBALI') ?></td>
                    <td style="padding: 1px 0; white-space: nowrap;"><?= to_currency($amount_change) ?></td>
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
