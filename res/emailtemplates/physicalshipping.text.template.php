<?php
$shippingAddress = !empty($orderData['shipping']) 
                 ? $orderData['shipping']
                 : ((!empty($orderData['invoice'])) ? $orderData['invoice'] : false);

echo $l10n->get('cart_order_no') . ": " . $orderData['order']['id'] . "\n\n";

echo str_replace("<br />", "\n", $opening) . "\n\n";

echo $l10n->get('cart_product_list') . "\n\n";

foreach ($products as $value) {
    $url = $baseurl . "cart/x5cart.php?download=" . $value['download_hash'];
    echo "\t" . $value['quantity'] . " " . $value['name'] . "\n";
}
echo "\n";

// Shipping Data
if ($shippingAddress && is_array($shippingAddress)) {
    echo str_replace("<br />", "\n", $l10n->get('cart_shipping_address')) . "\n\n";
    $i = 0;
    foreach ($shippingAddress as $key => $value) {
        if (trim($value['value']) != "") {
            echo "\t" . $value['label'] . ": " . $value['value'] . "\n";
            $i++;
        }
    }
    echo "\n";
}

// Shipping Text
if (isset($shippingData) && is_array($shippingData)) {
    echo str_replace("\\n", "\n", $l10n->get('cart_shipping') . "\n" . $shippingData['name'] .  "\n" . $shippingData['email_text']) . "\n\n";
    // Tracking Info 
    if (isset($shippingData['tracking_type']) && isset($orderData['order']['tracking_code']) && $shippingData['tracking_type'] === 'url' && $orderData['order']['tracking_code'] !== null) {
        echo $l10n->get('email_tracking_info');
        echo "\n";
        if ($shippingData['tracking_url'] !== '') {
            echo "" . str_replace('[TRACKING_CODE]', $orderData['order']['tracking_code'], $shippingData['tracking_url']) . "\n";
        }
        echo "" . $l10n->get('email_tracking_code') . " " . $orderData['order']['tracking_code'];
        echo "\n\n";
    }
}
    
echo str_replace("<br />", "\n", $closing);

