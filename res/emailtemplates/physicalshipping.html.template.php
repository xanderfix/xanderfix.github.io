<?php
    // This is the email separator line
    $separator = "<tr><td colspan=\"2\" style=\"margin: 10px 0; height: 10px; font-size: 0.1px; border-bottom: 1px solid [email:emailBackground];\">&nbsp;</td></tr>";
    $shippingAddress = !empty($orderData['shipping']) 
                     ? $orderData['shipping']
                     : ((!empty($orderData['invoice'])) ? $orderData['invoice'] : false);
?>
<table border="0" width="100%" style="[email:contentStyle]">
    <tr>
        <td style="[email:contentFontFamily] text-align: center; font-weight: bold;font-size: 1.11em">
            <?php echo $l10n->get('cart_order_no') . ": " . $orderData['order']['id'] ?>
        </td>
    </tr>
    <!-- Opening Message -->
    <tr><td style="[email:contentFontFamily]"><?php echo $opening ?></td></tr>
    <?php echo $separator ?>
    <!-- Order Data -->
    <tr>
        <td style="[email:contentFontFamily] padding: 5px 0 0 0;">
            <h3 style="font-size: 1.11em"><?php echo $l10n->get('cart_product_list') ?></h3>
            <table cellpadding="5" width="100%" style="[email:contentStyle] border-collapse: collapse;">
                <tr bgcolor="[email:bodyBackgroundOdd]">
                    <td style="[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; color: [email:bodyTextColorOdd]">
                        <b><?php echo $l10n->get("cart_name") ?></b>
                    </td>
                    <td style="[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; color: [email:bodyTextColorOdd]">
                        <b><?php echo $l10n->get("cart_qty") ?></b>
                    </td>
                </tr>    
                <?php $i = 0; foreach ($products as $value): ?>
                    <?php $url = $baseurl . "cart/x5cart.php?download=" . $value['download_hash']; ?>
                    <tr valign="top" style="[email:contentFontFamily] vertical-align: top"<?php ($i%2 ? " bgcolor=\"#EEEEEE\"" : "") ?>>
                        <td style="[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];"><?php echo $value["name"] ?><br /><?php echo $value["description"] ?></td>
                        <td style="[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder]; text-align: right;"><?php echo $value["quantity"] ?></td>
                        <?php if (isset($value['image'])): ?>
                            <td style="[email:contentFontFamily] border: 1px solid [email:bodyBackgroundBorder];"><br /><img src="<?php echo $baseurl . $value['image'] ?>" alt="" style="max-width: 250px;" /></td>
                        <?php endif; ?>
                    </tr>
                    <?php $i++; ?>
                <?php endforeach; ?>
            </table>
        </td>
    </tr>
    <?php if ($shippingAddress && is_array($shippingAddress)): ?>
    <?php echo $separator ?>
    <!-- Invoice & Shipping Data -->
    <tr style="vertical-align: top" valign="top">
        <!-- Data header -->
        <td colspan="2" style="[email:contentFontFamily]">
            <h3 style="font-size: 1.11em"><?php echo $l10n->get('cart_shipping_address') ?></h3>
            <table width="100%" style="[email:contentStyle]">
            <?php $i = 0; foreach ($shippingAddress as $key => $value): ?>
                <?php if (trim($value['value']) != ""): ?>
                <tr <?php echo ($i%2 ? " bgcolor=\"[email:bodyBackgroundOdd]\"" : "") ?>>
                    <?php if (preg_match('/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $value['value'])): ?>
                    <!-- Email -->
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><b><?php echo str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) ?>:</b></td>
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><a href="mailto: <?php echo $value['value'] ?>"><?php echo $value['value'] ?></a></td>
                    <?php elseif (preg_match('/^http[s]?:\/\/[a-zA-Z0-9\.\-]{2,}\.[a-zA-Z]{2,}/', $value['value'])): ?>
                    <!-- URL -->
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><b><?php echo str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) ?>:</b></td>
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><a href="<?php echo $value['value'] ?>"><?php $value['value'] ?></a></td>
                    <?php else: ?>
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><b><?php echo str_replace(array("\\'", '\\"'), array("'", '"'), $value['label']) ?>:</b></td>
                    <?php
                        // Attachment file: strip its name removing the timestamp prefix
                        if ($value['id'] == 'Attachment') {
                            $splitedFileName = explode("_", $value['value'], 2);
                            $fieldValue = $splitedFileName[1];
                        }
                        else
                            $fieldValue = $value['value'];  
                    ?>
                    <td style="[email:contentFontFamily]<?php echo ($i%2 ? " color: [email:bodyTextColorOdd]" : "") ?>"><?php echo str_replace(array("\\'", '\\"'), array("'", '"'), $fieldValue) ?></td>
                    <?php endif; ?>
                </tr>
                    <?php $i++; ?>
                <?php endif; ?>
            <?php endforeach; ?>
            </table>
        </td>
    </tr>
    <?php endif; ?>
    <!-- Shipping Text -->
    <?php if (isset($shippingData) && is_array($shippingData)): ?>
    <?php echo $separator ?>
    <tr>
        <td colspan="2" style="[email:contentFontFamily] padding: 5px 0 0 0;">
            <h3 style="font-size: 1.11em"><?php echo $l10n->get('cart_shipping') ?></h3>
            <?php echo nl2br(str_replace("\\n","<br />",$shippingData['name'])) ?>
            <div><?php echo nl2br(str_replace("\\n","<br />",$shippingData['email_text'])) ?></div>
        </td>
    </tr>
    <!-- Tracking Info -->
        <?php if (isset($shippingData['tracking_type']) && isset($orderData['order']['tracking_code']) && $shippingData['tracking_type'] === 'url' && $orderData['order']['tracking_code'] !== null) : ?>
    <?php echo $separator ?>
    <tr>
        <td colspan="2" style="[email:contentFontFamily] padding: 5px 0 0 0;">
            <div><?php echo $l10n->get('email_tracking_info') ?></div>
            <?php if ($shippingData['tracking_url'] !== '') : ?>
            <div><?php echo str_replace('[TRACKING_CODE]', $orderData['order']['tracking_code'], $shippingData['tracking_url']) ?></div>
            <?php endif; ?> 
            <br>
            <div><?php echo $l10n->get('email_tracking_code') ?> <?php echo $orderData['order']['tracking_code'] ?></div>
        </td>
    </tr>   
        <?php endif; ?>    
    <?php endif; ?>
    <!-- Closing message -->
    <?php echo $separator ?>
    <tr><td style="[email:contentFontFamily] padding: 5px 0 0 0;"><?php echo $closing ?></td></tr>
</table>


