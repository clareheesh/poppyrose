<?php

add_action('frm_date_field_js', 'pr_limit_date_fields');
function pr_limit_date_fields($field_id)
{
	// Using stringpos allows me to filter the fields which may come from cloned forms, where a number is added to the
	// end of the key
	if (strpos($field_id, 'field_date') !== false) { //change FIELDKEY to the key of your date field

		$forbid = get_field('invalid_dates', 'option');
		$forbidden_dates = '';

		if ($forbid && (is_array($forbid) || is_object($forbid))) {
			foreach ($forbid as $f) {
				$forbidden_dates .= ' && d != "' . date('Y-n-d', strtotime(str_replace('/', '-', $f['date']))) . '"';
			}
		}

		$min_day = '1';
		if (get_field('allow_orders_today', 219) && is_single(219)) {
			$min_day = '0';
		}

		echo ',minDate:' . $min_day . ',beforeShowDay: function(date){var day=date.getDay();var month = date.getMonth() + 1;var d = date.getFullYear() + "-" + month + "-" + date.getDate();return [(day != 0 && day != 6 ' . $forbidden_dates . ')];}';
	}
}


/** Allow textarea fields to be saved in woocommerce order submissions */
add_filter('wc_fp_exclude_fields', 'frm_adjust_exclude_fields');
function frm_adjust_exclude_fields($exclude)
{
	$key = array_search('textarea', $exclude);
	if ($key !== false) {
		unset($exclude[$key]);
	}
	return $exclude;
}


/** Remove unnecessary billing fields from woocommerce checkout */
add_filter('woocommerce_checkout_fields', 'poppy_override_checkout');
function poppy_override_checkout($fields)
{

//	unset($fields['billing']['billing_first_name']);
//	unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_address_1']);
	unset($fields['billing']['billing_address_2']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);
	unset($fields['billing']['billing_state']);
//	unset($fields['billing']['billing_phone']);
	unset($fields['order']['order_comments']);
//	unset($fields['billing']['billing_email']);
//	unset($fields['account']['account_username']);
//	unset($fields['account']['account_password']);
//	unset($fields['account']['account_password-2']);

	return $fields;
}

/** Relabel "Add to Cart" to "Add to Order" */
add_filter('woocommerce_product_single_add_to_cart_text', 'poppy_custom_basket_text');    // 2.1 +
function poppy_custom_basket_text()
{
	return __('Add to Order', 'woocommerce');
}

/** Remove additional information box */
add_filter('woocommerce_enable_order_notes_field', '__return_false');
/** Remove unnecessary product tabs */
add_filter('woocommerce_product_tabs', '__return_empty_array');


/** Dynamically populate the dropdown field in the order form with the list of suburbs */
add_filter('frm_setup_new_fields_vars', 'populate_available_suburbs', 20, 2);
function populate_available_suburbs($values, $field)
{

	if (strpos($field->field_key, 'suburb') !== false) { //replace 125 with the ID of the field to populate

		$suburbs = get_field('available_suburbs', 'options');

		unset($values['options']);
		$values['options'] = [];

		foreach ($suburbs as $suburb) {
			$add_value = $suburb['additional_price'] ? $suburb['additional_price'] : 0;
			$value_label = $add_value > 0 ? '$' . $add_value . '.00' : '';

			$values['options'][] = array(
				'label' => $suburb['suburb'] . ' ' . $value_label,
				'value' => $suburb['suburb'] . ' $' . $add_value
			);

//			$values['options'][$suburb['postcode'] . ' ' . $suburb['suburb'] . ' $' . $add_value ] = $suburb['suburb'] . ' ' . $suburb['postcode'] . $value_label;
		}

//		$values['use_key'] = true; // this will set the field to save the value instead of the label
	}

	return $values;
}


function get_orders()
{
	$orders = get_posts(array(
		'post_type' => 'shop_order',
		'post_status' => array_keys(wc_get_order_statuses())
	));

	return $orders;
}


// Add order export menu page
add_action('admin_menu', 'poppy_order_export');
function poppy_order_export()
{
	add_options_page('Order Export', 'Order Export', 'manage_options', 'poppy-order-export', 'poppy_order_export');
}

function poppy_myme_types($mime_types)
{
	$mime_types['kml'] = 'application/vnd.google-earth.kml+xml '; //Adding svg extension
	return $mime_types;
}

add_filter('upload_mimes', 'poppy_myme_types', 1, 1);


add_shortcode('front_page_banner', 'front_page_banner');
function front_page_banner()
{ ?>
	<!-- Front Page Banner -->
	<?php
	$text = get_field('text');
	$image = get_field('picture');
	$image_height = $image ? $image['sizes']['medium_large-height'] : false;
	$image_width = $image ? $image['sizes']['medium_large-width'] : false;
	$image = $image ? $image['sizes']['medium_large'] : false;
	$product = get_field('product');
	$product_thumb = wp_get_attachment_image_src(get_post_thumbnail_id($product), 'medium_large');
	$image = $product && has_post_thumbnail($product) ? $product_thumb[0] : $image;
	$image_height = $product && has_post_thumbnail($product) ? $product_thumb[2] : $image_height;
	$image_width = $product && has_post_thumbnail($product) ? $product_thumb[1] : $image_height;
	$direction = get_field('picture_orientation');
	$show_label = get_field('display_label');
	$label_shape = get_field('label_shape');
	$label_bg = get_field('label_background_colour');
	$label_text = get_field('label_text');
	$label_colour = get_field('label_text_colour');
	?>

	<div class="banner">

		<?php if ($direction == 'right') : ?>
			<div class="text-container match">
				<div class="text"><?= $text; ?></div>
			</div>
		<?php endif; ?>

		<?php if ($image) : ?>
			<div class="picture-container match">
				<?= $product ? '<a href="' . get_the_permalink($product) . '">' : ''; ?>
				<img src="<?= $image; ?>"/>
				<?= $product ? '</a>' : ''; ?>

				<?php if ($show_label) : ?>

					<div class="label <?= $label_shape; ?>" style="background-color: <?= $label_bg; ?>">
						<p class="label-text" style="color: <?= $label_colour; ?>"><?= $label_text; ?></p>
					</div>

				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ($direction == 'left') : ?>
			<div class="text-container match">
				<div class="text"><?= $text; ?></div>
			</div>
		<?php endif; ?>

	</div>

	<!-- End Banner --> <?php
}

function get_product_id_from_cart_input_item($input_name)
{

	global $woocommerce;
	$cart_item_keys = $woocommerce->cart->get_cart();

	$start = strpos($input_name, '[') + 1;
	$end = strpos($input_name, ']');
	$length = $end - $start;

	$product_key = isset($input_name) ? substr($input_name, $start, $length) : false;

	$product_id = isset($cart_item_keys[$product_key]) ? $cart_item_keys[$product_key]['product_id'] : null;

	return $product_id;
}


function outputCSV($header, $data)
{
	$output = fopen("php://output", "w");
	fputcsv($output, $header);
	foreach ($data as $row) {
		fputcsv($output, $row); // here you can change delimiter/enclosure
	}
	fclose($output);
}


// Reorder the WooCommerce layout
add_action('wp_head', 'reorder_woocommerce_form_products');
function reorder_woocommerce_form_products()
{
	$has_form = get_post_meta(get_the_ID(), '_attached_formidable_form', true);

	if ($has_form && get_field('form_below')) {
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50);
		remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);

		add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_add_to_cart', 5);
		add_action('woocommerce_after_single_product_summary', 'add_extra_wrapper_start', 4);
		add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_meta', 6);
		add_action('woocommerce_after_single_product_summary', 'woocommerce_template_single_sharing', 7);
		add_action('woocommerce_after_single_product_summary', 'add_extra_wrapper_end', 8);
	}

}

function add_extra_wrapper_start()
{
	echo '<div class="add-to-cart-summary" style="clear:both;">';
}

function add_extra_wrapper_end()
{
	echo '</div>';
}


/** Get a formdable key value from the id number */
function get_key_by_id($id)
{
	global $frmdb, $wpdb;
	$key = false;

	// Getting the field_key using the field id
	if (is_numeric($id) && $id != null & $id != '') {
		$key = $wpdb->get_var($wpdb->prepare("SELECT field_key FROM $frmdb->fields WHERE id=%s", $id));
	}

	return $key;
}


/** Change the number of products before pagination */
add_filter('loop_shop_per_page', create_function('$cols', 'return 24;'), 20);


add_action('woocommerce_cart_actions', 'move_proceed_button');
function move_proceed_button($checkout)
{
	if (get_field('checkout_button', 'option')) :
		echo '<a href="' . get_field('checkout_button_link', 'option') . '" class="checkout-button button alt wc-forward" >' . __(get_field('checkout_button_text', 'option'), 'woocommerce') . '</a>';
	endif;
}


/**
 * Add custom order statuses
 */
add_action('init', 'register_poppy_order_status');
function register_poppy_order_status()
{
	register_post_status('wc-awaiting-shipment', array(
		'label' => 'Awaiting shipment',
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop('Awaiting shipment <span class="count">(%s)</span>', 'Awaiting shipment <span class="count">(%s)</span>')
	));

	register_post_status('wc-awaiting-pickup', array(
		'label' => 'Awaiting pickup',
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop('Awaiting pickup <span class="count">(%s)</span>', 'Awaiting pickup <span class="count">(%s)</span>')
	));

	register_post_status('wc-due-for-delivery', array(
		'label' => 'Due for delivery',
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop('Due for delivery <span class="count">(%s)</span>', 'Due for delivery <span class="count">(%s)</span>')
	));
}

/** Add to list of WC Order Statuses */
add_filter('wc_order_statuses', 'add_poppy_order_status');
function add_poppy_order_status($order_statuses)
{

	$new_order_status = array();

	// add new order status after processing
	foreach ($order_statuses as $key => $status) {

		$new_order_status[$key] = $status;

		if ('wc-processing' === $key) {
			$new_order_status['wc-awaiting-shipment'] = 'Awaiting shipment';
			$new_order_status['wc-awaiting-pickup'] = 'Awaiting pickup';
			$new_order_status['wc-due-for-delivery'] = 'Due for delivery';
		}
	}

	return $new_order_status;
}


/** Add custom bulk action to match new statuses */
add_action('admin_footer-edit.php', 'poppy_bulk_edit_action');
function poppy_bulk_edit_action()
{
	global $post_type;

	if ($post_type == 'shop_order') {
		?>
		<script type="text/javascript">
			jQuery(document).ready(function ($) {

				$('<option>').val('mark_due_for_delivery').text('Mark as due for delivery').insertAfter("select[name='action'] > option[value='mark_processing']");
				$('<option>').val('mark_due_for_delivery').text('Mark as due for delivery').insertAfter("select[name='action2'] > option[value='mark_processing']");

				$('<option>').val('mark_awaiting_pickup').text('Mark as awaiting pickup').insertAfter("select[name='action'] > option[value='mark_processing']");
				$('<option>').val('mark_awaiting_pickup').text('Mark as awaiting pickup').insertAfter("select[name='action2'] > option[value='mark_processing']");

				$('<option>').val('mark_awaiting_shipment').text('Mark as awaiting shipment').insertAfter("select[name='action'] > option[value='mark_processing']");
				$('<option>').val('mark_awaiting_shipment').text('Mark as awaiting shipment').insertAfter("select[name='action2'] > option[value='mark_processing']");

			});
		</script>
		<?php
	}
}

/** Add the custom bulk actions */
add_action('load-edit.php', 'poppy_mark_custom_bulk_status');
function poppy_mark_custom_bulk_status()
{
	global $typenow;
	$post_type = $typenow;
	$sendback = admin_url('edit.php?post_type=shop_order&success=1');
	$orderids = [];

	if ($post_type == 'shop_order') {

		$wp_list_table = _get_list_table('WP_Posts_List_Table');
		$action = $wp_list_table->current_action();
		$allowed_actions = array("mark_awaiting_shipment", "mark_awaiting_pickup", "mark_due_for_delivery");


		if (!in_array($action, $allowed_actions))
			return;

		if (isset($_REQUEST['post'])) {
			$orderids = array_map('intval', $_REQUEST['post']);
		}

		$status = false;

		switch ($action) {
			case 'mark_awaiting_shipment' :
				$status = 'wc-awaiting-shipment';
				break;
			case 'mark_awaiting_pickup' :
				$status = 'wc-awaiting-pickup';
				break;
			case 'mark_due_for_delivery' :
				$status = 'wc-due-for-delivery';
				break;
			default :
				return;
		}

		if ($status) {
			foreach ($orderids as $orderid) {
				$order = new WC_Order($orderid);

				$order->update_status($status, 'order_note');
				// send email?
			}
		}

		wp_redirect($sendback);
		exit;
	}
}

add_action('woocommerce_order_status_awaiting-shipment_to_due-for-delivery', 'send_due_for_delivery_email');
function send_due_for_delivery_email($order_id)
{
	$mailer = WC()->mailer();
	$mails = $mailer->get_emails();
	if (!empty($mails)) {
		foreach ($mails as $mail) {
			if ($mail->id == 'wc_due_for_delivery') {
				$mail->trigger($order_id);
			}
		}
	}
}

/** Custom CSS for the new statuses */
add_action('admin_head', 'insert_new_css_poppy');
function insert_new_css_poppy()
{
	echo '<style>
   mark.awaiting-shipment.tips { background: orange; border-radius: 50%; -moz-border-radius: 50%; -webkit-border-radius: 50%; }
   mark.awaiting-pickup.tips { background: #ad8ad8; border-radius: 50%; -moz-border-radius: 50%; -webkit-border-radius: 50%; }
   mark.due-for-delivery.tips { background: #4d78ff; border-radius: 50%; -moz-border-radius: 50%; -webkit-border-radius: 50%; }
   </style>';
}

/**
 * wc_remove_related_products
 *
 * Clear the query arguments for related products so none show.
 * Add this code to your theme functions.php file.
 */
function wc_remove_related_products($args)
{
	return array();
}

add_filter('woocommerce_related_products_args', 'wc_remove_related_products', 10);


/**
 * Add checkbox field to the checkout
 **/
add_action('woocommerce_after_order_notes', 'my_custom_checkout_field');

function my_custom_checkout_field($checkout)
{

	$terms = get_field('terms_message', 'options');

	if (!$terms) {

		$terms = '<label class="checkbox terms" for="terms_poppy"><strong>I understand that no time frames can be guaranteed on my delivery.</strong><br>' .
			'I have checked that all of the details I have provided are correct and understand that incorrect details may result in the need to pay an additional courier fee.<br>' .
			'I can confirm that the property that is being delivered to is not secure and your courier will be able to access the front door. ' .
			'If this is not possible, the flowers may have to be returned to the studio and a re-delivery fee may be applicable.</label>';
	} else {
		$terms = '<label class="checkbox terms" for="terms_poppy">' . $terms . '</label>';
	}

	echo '<div id="terms"><h3>Terms of Purchase</h3>';

	woocommerce_form_field('terms_poppy', array(
		'type' => 'checkbox',
		'class' => array('input-checkbox', 'terms'),
		'label' => $terms,
		'required' => true,
	), $checkout->get_value('terms_poppy'));

	echo '</div>';
}

/**
 * Process the checkout
 **/
add_action('woocommerce_checkout_process', 'my_custom_checkout_field_process');

function my_custom_checkout_field_process()
{
	global $woocommerce;

	// Check if set, if its not set add an error.
	if (!$_POST['terms_poppy'])
		$woocommerce->add_error(__('Please agree to the terms.'));
}


// ADDING COLUMN TITLES (Here 2 columns)
add_filter('manage_edit-shop_order_columns', 'custom_shop_order_column', 11);
function custom_shop_order_column($columns)
{
	$new_columns = [];

	//add columns
	foreach ($columns as $key => $col) {
		$new_columns[$key] = $col;
		if ($key == 'order_date') {
			$new_columns['delivery-date'] = 'Delivery Date';
		}
	}

	return $new_columns;
}

add_filter('manage_edit-shop_order_sortable_columns', 'poppy_sortable_delivery_date');
function poppy_sortable_delivery_date($columns)
{
	$columns['delivery-date'] = 'delivery-date';
	return $columns;
}

add_action('pre_get_posts', 'poppy_sort_delivery_date');
function poppy_sort_delivery_date($query)
{
	if (!is_admin()) {
		return;
	}

	$orderby = $query->get('orderby');

	if ('delivery-date' == $orderby) {
		$query->set('orderby', 'meta_value_num');
		$query->set('meta_key', 'delivery_date');
	}
}


// adding the data for each orders by column (example)
add_action('manage_shop_order_posts_custom_column', 'poppy_custom_delivery_date', 10, 2);
function poppy_custom_delivery_date($column)
{
	global $post, $woocommerce, $the_order;
	$order_id = $the_order->id;

	$order_items = $the_order->get_items();
	$delivery_dates = [];

	foreach ($order_items as $item) {
		if (isset($item['Delivery Date'])) {
			if (strtotime($item['Delivery Date'])) {
				$delivery_dates[] = date('Y/m/d', strtotime($item['Delivery Date']));
			} else {
				$delivery_dates[] = $item['Delivery Date'];
			}
		}
	}

	switch ($column) {
		case 'delivery-date' :
			$myVarOne = implode(', ', $delivery_dates);
			echo $myVarOne;
			break;
	}
}


/** Extend order search functionality */
add_filter('woocommerce_shop_order_search_fields', 'woocommerce_shop_order_search_order_total');
function woocommerce_shop_order_search_order_total($search_fields)
{

	$search_fields[] = 'delivery_date_search';
	return $search_fields;
}


/** Save delivery date on order */
add_action('woocommerce_checkout_order_processed', 'poppy_save_delivery_date');
function poppy_save_delivery_date($order_id)
{
	$order = new WC_Order($order_id);
	$items = $order->get_items();

	foreach ($items as $key => $item) {
		if (isset($item['Delivery Date'])) {
			if (strtotime($item['Delivery Date'])) {
				add_post_meta($order_id, 'delivery_date', strtotime($item['Delivery Date']));
				add_post_meta($order_id, 'delivery_date_search', date('Y/m/d', strtotime($item['Delivery Date'])));
			} else {
				add_post_meta($order_id, 'delivery_date', $item['Delivery Date']);
				add_post_meta($order_id, 'delivery_date_search', $item['Delivery Date']);
			}
		}
	}
}


add_action('woocommerce_order_status_wc-order-confirmed', array(WC(), 'send_transactional_email'), 10, 10);


/** Add custom email notification class */
function add_due_for_shipment_email_notification($email_classes)
{
	require('class-wc-due-for-delivery-email.php');

	$email_classes['WC_Due_For_Delivery_Email'] = new WC_Due_For_Delivery_Email();

	return $email_classes;
}

add_filter('woocommerce_email_classes', 'add_due_for_shipment_email_notification');


/**
 * Register "woocommerce_order_status_pending_to_quote" as an email trigger
 */
add_filter('woocommerce_email_actions', function ($email_actions) {
//	$email_actions[] = 'woocommerce_order_status_due-for-delivery';
	$email_actions[] = 'woocommerce_order_status_awaiting-shipment_to_due-for-delivery';
//	var_dump($email_actions);
//	exit;
	return $email_actions;
});


/** Whenever the order date is updated, make sure the search date is updated as well */
add_action('save_post', 'pr_update_date_for_searching');
function pr_update_date_for_searching($post_id)
{
	$delivery_date_search = get_post_meta($post_id, 'delivery_date_search', true);
	update_post_meta($post_id, 'delivery_date', strtotime($delivery_date_search));
}


function mark_orders_complete()
{
	$orders = get_posts(array(
		'post_type' => 'shop_order',
		'post_status' => 'wc-due-for-delivery'
	));

	foreach ($orders as $order) {
		$order->post_status = 'wc-completed';
		wp_update_post($order);
	}

	return $orders;
}


add_filter('frm_validate_field_entry', 'limit_chars', 10, 3);
function limit_chars($errors, $posted_field, $posted_value)
{
	if (strpos($posted_field->field_key, 'limitchars') !== false && $posted_value != '') { //change 25 to the ID of the field to validate
		//check the $posted_value here
		$count = strlen($posted_value); //uncomment this line to count characters instead of words

		$char = get_field('message_max_chars', 'option') ? get_field('message_max_chars', 'option') : 100;

		//uncomment the next two lines create a maximum value and error message
		if ($count > $char) //change "300" to fit your maximum limit
			$errors['field' . $posted_field->id] = 'Character limit is ' . $char . '. Your message is currently ' . $count . ' characters long. Please shorten.';
	}
	return $errors;
}