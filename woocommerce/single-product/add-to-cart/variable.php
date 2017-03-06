<?php
/**
 * Variable product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/variable.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see    https://docs.woothemes.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.5.0
 */
if (!defined('ABSPATH')) {
	exit;
}

global $product;

$attribute_keys = array_keys($attributes);
$variations = $product->get_available_variations();
$selected_available = true;

//?><!--<pre>--><?php //var_dump($product->get_available_variations()); ?><!--</pre>--><?php

do_action('woocommerce_before_add_to_cart_form'); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->id); ?>" data-product_variations="<?php echo htmlspecialchars(json_encode($available_variations)) ?>">
	<?php do_action('woocommerce_before_variations_form'); ?>

	<?php if (empty($available_variations) && false !== $available_variations) : ?>
		<p class="stock out-of-stock"><?php _e('This product is currently out of stock and unavailable.', 'woocommerce'); ?></p>
	<?php else : ?>

		<?php if ( get_field('hide_variations') ) : ?>
			<?php foreach ($attributes as $attribute_name => $options) : ?>

				<?php if ($variations && (is_array($variations) || is_object($variations))) : ?>
					<div class="variations-sexy">
						<?php $count = count($variations); ?>
						<?php $columns = $count % 4; ?>
						<div class="row">
							<?php foreach ($variations as $variation) { ?>
								<?php $find_key = 'attribute_' . strtolower($attribute_name); ?>
								<?php $pos = isset($variation['attributes'][$find_key]) ? array_search($variation['attributes'][$find_key], $options) : false; ?>

								<?php if ($pos !== false) : ?>

									<?php $selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) ? wc_clean(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)])) : $product->get_variation_default_attribute($attribute_name); ?>
									<?php if ($selected == $options[$pos]) {
										if (!isset($variation['is_in_stock']) || !$variation['is_in_stock'])
											$selected_available = false;
									} ?>
									<div class="col-sm-<?= 12 / $count; ?> col-xs-12">
										<a href="#" class="variation-box <?= $selected == $options[$pos] ? 'selected' : ''; ?>" data-target-val="<?= $options[$pos]; ?>" data-available="<?= isset($variation['is_in_stock']) && $variation['is_in_stock'] ? '1' : '0'; ?>">
											<div class="text-center option">
												<?= $options[$pos]; ?>
											</div>
										</a>
									</div>

								<?php endif;
							} ?>
						</div>
					</div>
				<?php endif; ?>

			<?php endforeach; ?>
		<?php endif; ?>

		<div class="variations <?= get_field('hide_variations') ? 'hide' : ''; ?>">
			<?php foreach ($attributes as $attribute_name => $options) : ?>

				<label for="<?php echo sanitize_title($attribute_name); ?>"><?php echo wc_attribute_label($attribute_name); ?></label>

				<div class="value">
					<?php
					$selected = isset($_REQUEST['attribute_' . sanitize_title($attribute_name)]) ? wc_clean(urldecode($_REQUEST['attribute_' . sanitize_title($attribute_name)])) : $product->get_variation_default_attribute($attribute_name);
					wc_dropdown_variation_attribute_options(array(
						'options' => $options, 'attribute' => $attribute_name, 'product' => $product,
						'selected' => $selected
					));
					echo end($attribute_keys) === $attribute_name ? apply_filters('woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __('Clear', 'woocommerce') . '</a>') : '';
					?>
				</div>

			<?php endforeach; ?>
		</div>

		<?php if (get_field('unavailable_message')) : ?>
			<div class="unavailable_message" <?= $selected_available ? 'style="display:none"' : ''; ?>>
				<?php the_field('unavailable_message'); ?>
			</div>
		<?php endif; ?>

		<?php do_action('woocommerce_before_add_to_cart_button'); ?>

		<div class="single_variation_wrap">
			<?php
			/**
			 * woocommerce_before_single_variation Hook.
			 */
			do_action('woocommerce_before_single_variation');

			/**
			 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
			 * @since 2.4.0
			 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
			 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
			 */
			do_action('woocommerce_single_variation');

			/**
			 * woocommerce_after_single_variation Hook.
			 */
			do_action('woocommerce_after_single_variation');
			?>
		</div>

		<?php do_action('woocommerce_after_add_to_cart_button'); ?>
	<?php endif; ?>

	<?php do_action('woocommerce_after_variations_form'); ?>
</form>

<?php
do_action('woocommerce_after_add_to_cart_form');
