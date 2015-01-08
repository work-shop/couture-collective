<?php

$id = $GLOBALS['CC_POST_DATA']['id'];
$share_price = get_field('dress_share_price', $id);
$sale_price = get_field('dress_sale_price', $id);
$rental_price = get_field('dress_rental_price', $id);

?>

<p class="h7">SHARE: <span class="numerals h8"><?php echo sprintf('$%s', number_format($share_price, 2, '.', ',')); ?></span></p>

<p class="h7">RENTAL: <span class="numerals h8"><?php echo sprintf('$%s', number_format($rental_price, 2, '.', ',')); ?></span></p>

<?php if ( $GLOBALS["CC_POST_DATA"]['sale']->is_in_stock() ) : ?>

<p class="h7">END OF SEASON SALE: <span class="numerals h8"><?php echo sprintf('$%s', number_format($sale_price, 2, '.', ',')); ?></span></p>

<?php endif; ?>