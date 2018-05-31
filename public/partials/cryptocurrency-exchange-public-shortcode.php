<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://wpressian.com
 * @since      1.0.0
 *
 * @package    Cryptocurrency_Exchange
 * @subpackage Cryptocurrency_Exchange/public/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="cryptocurrency-ticker">
    <ul class="cryptocurrency-ticker__data">
        <?php foreach($subscription as $coin_code => $coin_name) { ?>
        <li id="cryptocurrency-ticker__item-<?=$coin_code;?>" class="cryptocurrency-ticker__item">
            <span class="cryptocurrency-ticker__cur-name"><?=$coin_name;?></span>
            <span id="cryptocurrency-ticker__cur-PRICE_<?=$coin_code;?>" class="cryptocurrency-ticker__cur-PRICE_<?=$coin_code;?>"></span>
            <span id="cryptocurrency-ticker__cur-CHANGE24HOURPCT_<?=$coin_code;?>" class="cryptocurrency-ticker__cur-CHANGE24HOURPCT_<?=$coin_code;?>"></span>
        </li>
        <?php } ?>
    </ul>
    <input type="hidden" class="cryptocurrency-ticker__subscribe" value="<?=$subscription_keys; ?>" />
</div>