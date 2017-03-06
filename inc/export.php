<?php

function build_date_query($from = false, $to = false)
{

	if (!$from) {
		$from = date('d-m-Y');
	}

	if (!$to) {
		$to = date('d-m-Y');
	}

	// Ensure dates are in the correct format
	$to = str_replace('/', '-', $to);
	$from = str_replace('/', '-', $from);

	if (!strtotime($to)) {
		$to = date('d-m-Y', $to);
	}

	if (!strtotime($from)) {
		$from = date('d-m-Y', $from);
	}

	// Build date query
	$dq = array(
		'after' => $from,
		'before' => $to,
	);

	return $dq;
}


function build_meta_query($from = false, $to = false)
{

	if (!$from) {
		$from = date('d-m-Y');
	}

	if (!$to) {
		$to = date('d-m-Y');
	}

	// Ensure dates are in the correct format
	$to = str_replace('/', '-', $to);
	$from = str_replace('/', '-', $from);

	if (!strtotime($to)) {
		$to = date('d-m-Y', $to);
	}

	if (!strtotime($from)) {
		$from = date('d-m-Y', $from);
	}

	$from = strtotime($from);
	$to = strtotime($to);

	// Build date query
	$mq = array(
		array(
			'key' => 'delivery_date',
			'value' => [$from, $to],
			'compare' => 'BETWEEN',
			'type' => 'numeric'
		)
	);

	return $mq;
}


function get_orders_poppy($max_results = -1, $from = null, $to = null, $type = 'delivery')
{

	$args = array(
		'posts_per_page' => $max_results,
		'post_type' => wc_get_order_types(),
		'post_status' => array_keys(wc_get_order_statuses()),
	);

	if ($type == 'delivery') {
		$mq = build_meta_query($from, $to);
		$args['meta_query'] = $mq;
	} else {
		$dq = build_date_query($from, $to);
		$args['date_query'] = $dq;
	}

	$orders = get_posts($args);

	return $orders;
}

function get_order_items_poppy($max_orders = -1, $from = null, $to = null, $type = 'items', $delivery = 'delivery')
{

	$orders = get_orders_poppy($max_orders, $from, $to);
	$items = [];
	$keys = [];

	if ($orders && (is_array($orders) || is_object($orders))) {
		foreach ($orders as $order) {
			$order_id = $order->ID;
			$order_date = $order->post_date;
			$order = new WC_Order($order);

			if ($type == 'items') {

				$order_items = $order->get_items();

				if ($order_items && (is_object($order_items) || is_array($order_items))) {
					foreach ($order_items as $item) {
						// Hide the following keys
						unset($item['type']);
						unset($item['item_meta']);
						unset($item['item_meta_array']);
						unset($item['line_tax_data']);
						// End removing keys
						// temp remove tax?
						unset($item['line_tax']);
						unset($item['line_subtotal_tax']);
						unset($item['tax_class']);

						if (!isset($item['Delivery Date']) && $delivery == 'delivery') {
							continue;
						} else {

							$item['order_id'] = $order_id;
							$item['order_date'] = $order_date;
							$items[] = $item;
							$keys = array_unique(array_merge($keys, array_keys($item)));
						}
					}
				}
			} else {
				$keys = [
					'order_id', 'order_date', 'purchased', 'total_cost', 'customer_name', 'customer_email',
					'payment_method', 'payment_method_title', 'order_status'
				];

				$items[] = [
					'order_id' => $order->id,
					'order_date' => $order->order_date,
					'purchased' => $order->get_item_count(),
					'total_cost' => $order->order_total,
					'customer_name' => $order->billing_first_name . ' ' . $order->billing_last_name,
					'customer_email' => $order->billing_email,
					'payment_method' => $order->payment_method,
					'payment_method_title' => $order->payment_method_title,
					'order_status' => $order->post_status,
				];
			}

		}
	}

	sort($keys);

	return array('keys' => $keys, 'items' => $items);
}

function create_order_export_array($max_orders = -1, $from = null, $to = null, $delivery = 'delivery')
{
	$items_keys = get_order_items_poppy($max_orders, $from, $to, $delivery);
}

// Create order export page
add_action('admin_menu', 'poppy_order_export_menu');
function poppy_order_export_menu()
{
	add_menu_page('Poppy Rose Export Orders', 'Order Export', 'manage_options', 'poppy-rose-order-export', 'poppy_order_export_page', '', 58);
}

add_action('admin_post_download_csv', 'pf_admin_download_csv');
function pf_admin_download_csv()
{
	session_start();

	$rows = $selected_rows = $keys = $headings = null;
	$is_template = false;

	if (isset($_POST['generate']) && $_POST['generate']) {
		// Generate based on selected rows

		$selected_rows = isset($_POST['selected_rows']) ? $_POST['selected_rows'] : null;
		$keys = isset($_POST['keys']) ? $_POST['keys'] : null;
	}

	if (isset($_POST['template_1']) && $_POST['template_1']) {
		// Generate based on template 1
		$is_template = true;
		$headings = [
			'Run Name',
			'Delivery ID',
			'Scheduled Date',
			'Where are your flowers going',
			'Customer Name (Business Name)',
			'Address 1',
			'Address 2',
			'Suburb',
			'State',
			'Post Code',
			'Contact Name',
			'Phone Number',
			'Delivery Instructions',
			'Product ID',
			'Product Description',
			'Qty',
			'Barcode',
			'To',
			'Message',
			'From'
		];
		$keys = [
			'',
			'order_id',
			'',
			'Where are your flowers going to?',
			'Recipient\'s Business Name (if applicable)',
			'',
			'',
			'',
			'',
			'',
			'Recipient\'s Full Name',
			'Recipient Phone Number',
			'Special Delivery Instructions',
			'',
			'',
			'',
			'',
			'To',
			'Message',
			'From'
		];
	}

	$rows = isset($_SESSION['export_rows']) ? $_SESSION['export_rows'] : null;

	$filename = "Poppy Rose Export " . date('d-m-Y');

	if ($keys && (is_array($keys) || is_object($keys)) && !$is_template) :
		foreach ($keys as $key) {
			$headings[] = ucwords(str_replace('_', ' ', stripslashes(html_entity_decode($key))));
		}
	endif;

	$output_rows = [];

	if ($rows && (is_array($rows) || is_object($rows))) :

		if ($is_template) {
			foreach ($rows as $i => $row) {

				// Get all of the rows
				if ($keys && (is_object($keys) || is_array($keys))) :
					foreach ($keys as $k => $key) {

						if ($key == '') {

							switch ($headings[$k]) {
								case 'Scheduled Date' :
									$output_rows[$i][] = isset($row['Delivery Date']) ? date('d-m-Y', strtotime($row['Delivery Date'])) : '';
									break;
								case 'Address 1' :
									$address = '';
									if (isset($row['Unit No.'])) {
										$address .= $row['Unit No.'] . ' ';
									}
									if (isset($row['Unit No (not street number) + building name and level if applicable'])) {
										$address .= $row['Unit No (not street number) + building name and level if applicable'] . ' ';
									}
									if (isset($row['Unit No (not street name) + building name and level if applicable'])) {
										$address .= $row['Unit No (not street name) + building name and level if applicable'] . ' ';
									}

									$address .= isset($row['Delivery Street Address']) ? html_entity_decode($row['Delivery Street Address']) : '';
									$address .= isset($row['Street number, street name']) ? html_entity_decode($row['Street number, street name']) : '';

									$output_rows[$i][] = $address;
									break;
								case 'Suburb' :
									$suburb = '';
									if (isset($row['Suburb and Postcode'])) {
										$str = html_entity_decode($row['Suburb and Postcode']);
										$str = strpos($str, '$') !== false ? substr($str, 0, strpos($str, '$')) : $str;
										$str = strpos($str, '+') !== false ? substr($str, 0, strpos($str, '+')) : $str;
										$suburb = preg_replace('/[0-9]+/', '', $str);
									} elseif (isset($row['Suburb & Postcode'])) {
										$str = html_entity_decode($row['Suburb & Postcode']);
										$str = strpos($str, '$') !== false ? substr($str, 0, strpos($str, '$')) : $str;
										$str = strpos($str, '+') !== false ? substr($str, 0, strpos($str, '+')) : $str;
										$suburb = preg_replace('/[0-9]+/', '', $str);
									}
									$output_rows[$i][] = $suburb;
									break;
								case 'State' :
									$output_rows[$i][] = 'QLD';
									break;
								case 'Post Code' :
									$postcode = '';
									if (isset($row['Suburb and Postcode'])) {
										$str = html_entity_decode($row['Suburb and Postcode']);
										$str = strpos($str, '$') !== false ? substr($str, 0, strpos($str, '$')) : $str;
										$str = strpos($str, '+') !== false ? substr($str, 0, strpos($str, '+')) : $str;
										$postcode = preg_replace('/[^0-9]/', '', $str);
									} elseif (isset($row['Suburb & Postcode'])) {
										$str = html_entity_decode($row['Suburb & Postcode']);
										$str = strpos($str, '$') !== false ? substr($str, 0, strpos($str, '$')) : $str;
										$str = strpos($str, '+') !== false ? substr($str, 0, strpos($str, '+')) : $str;
										$postcode = preg_replace('/[^0-9]/', '', $str);
									}
									$output_rows[$i][] = $postcode;
									break;
								case 'Product Description' :
									$prod_desc = '';
									if (isset($row['How Much Would You Like To Spend (including Delivery To Brisbane Metro. There May Be Additional Courier Charges To Other Suburbs)?'])) {
										$prod_desc .=  html_entity_decode($row['What style of flowers would you like?']) . ': ' . html_entity_decode($row['How much would you like to spend (including delivery to Brisbane metro. There may be additional courier charges to other suburbs)?']);
									} else if (isset($row['size'])) {
										$prod_desc .= $row['size'];
									} else if(isset($row['name'])) {
										$prod_desc .= $row['name'];
									}
									$output_rows[$i][] = $prod_desc;
									break;
								default :
									$output_rows[$i][] = '';
									break;
							}

						} else {

							$output_rows[$i][] = html_entity_decode($row[$key]);
						}
					}
				endif;
			}

		} else {

			// Get the rows that were selected on the order generation page and add them to the csv
			if ($selected_rows && (is_array($selected_rows) || is_object($selected_rows))) :
				foreach ($selected_rows as $i => $index) {
					if (isset($rows[$index])) :
						if ($keys && (is_object($keys) || is_array($keys))) :
							foreach ($keys as $key) {
								$output_rows[$i][] = html_entity_decode($rows[$index][$key]);
							}
						endif;
					endif;
				}
			endif;
		}
	endif;


	// create a file pointer connected to the output stream
	$output = fopen('php://output', 'w');

	// output headers so that the file is downloaded rather than displayed
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=' . $filename . '.csv');

	// output the column headings
	fputcsv($output, $headings);

	// loop over the rows, outputting them
	foreach ($output_rows as $row) {
		fputcsv($output, $row);
	}

	die;
}

add_action('admin_post_find_orders', 'poppy_order_export_page');
function poppy_order_export_page()
{

	session_start();

	if (!current_user_can('manage_options')) {
		wp_die(__("You do not have sufficient permission to access this page"));
	}
	?>

	<div class="wrap">
		<h1>Poppy Rose Order Export</h1>

		<p>Pull's all items due for delivery today by default. Enter a date range, or an order type to pull specific results.</p>

		<p>If you search for "Items" by "Delivery Date", then any items without Delivery Dates (aka, gift cards, etc), will not appear.</p>

		<div style="overflow: auto; margin-bottom: 30px;">
			<form action="" method="post">
				<input type="hidden" name="action" value="find_orders">
				<input type="text" value="<?= isset($_POST['from']) ? $_POST['from'] : date('d/m/Y'); ?>" name="from"/>
				<input type="text" value="<?= isset($_POST['to']) ? $_POST['to'] : date('d/m/Y'); ?>" name="to"/>
				<select name="date">
					<option value="delivery" <?= isset($_POST['date']) && $_POST['date'] == 'delivery' ? 'selected' : ''; ?>>Search by Delivery Date</option>
					<option value="order_date" <?= isset($_POST['date']) && $_POST['date'] == 'order_date' ? 'selected' : ''; ?>>Search by Order Date</option>
				</select>
				<select name="type">
					<option value="items" <?= isset($_POST['type']) && $_POST['type'] == 'items' ? 'selected' : ''; ?>>Items</option>
					<option value="order" <?= isset($_POST['type']) && $_POST['type'] == 'order' ? 'selected' : ''; ?>>Orders</option>
				</select>
				<input type="submit" value="Find Orders" name="submit">
			</form>
		</div>

		<?php

		$item_key = get_order_items_poppy(-1, isset($_POST['from']) ? $_POST['from'] : date('d/m/Y'), isset($_POST['to']) ? $_POST['to'] : date('d/m/Y'), isset($_POST['type']) ? $_POST['type'] : 'items', isset($_POST['date']) ? $_POST['date'] : 'delivery');
		$loop = isset($item_key['keys']) && $item_key['keys'] && (is_object($item_key['keys']) || is_array($item_key['keys'])) ? $item_key['keys'] : false;
		$items = isset($item_key['items']) && $item_key['items'] && (is_object($item_key['items']) || is_array($item_key['items'])) ? $item_key['items'] : false;
		$_SESSION['export_rows'] = $items;

		?>

		<style>
			table {
				background: white;
				padding: 10px;
				border-collapse: collapse;
			}

			thead {
				background: #efefef;
				border-bottom: 1px solid #aaa;
			}

			th {
				min-width: 100px;
				border: 1px solid #bbb;
			}

			tr {
				border-bottom: 1px solid #ddd;
			}

			td {
				border-right: 1px solid #efefef;
				padding: 10px;
				text-align: left;
			}

		</style>

		<form action="<?= admin_url('admin-post.php'); ?>" method="post">

			<p><input type="submit" name="template_1" value="Download Pre-Filled Template - Will be different to table below"/></p>
			
			<input type="hidden" name="action" value="download_csv">

			<div class="table table-responsive" style="overflow-x: auto">
				<table id="table">
					<?php if ($loop) { ?>
						<thead>
						<tr>
							<th style="width: 30px; min-width:30px;"></th>
							<?php foreach ($loop as $i => $key) { ?>

								<th class="_<?= $i; ?>">
									<input type="checkbox" name="keys[]" checked value="<?= $key; ?>"/>
									<?= ucwords(str_replace('_', ' ', $key)); ?>
									<br><a href="#" class="click-hide" style="text-transform: uppercase; font-size: 10px;">Hide</a>
								</th>

							<?php } ?>
						</tr>
						</thead>
					<?php } ?>

					<?php if ($items) : ?>
						<tbody>
						<?php foreach ($items as $row => $item) : ?>
							<tr>
								<td><input type="checkbox" name="selected_rows[]" checked value="<?= $row ?>"/></td>
								<?php if ($loop) : ?>
									<?php foreach ($loop as $i => $key) : ?>
										<td class="_<?= $i; ?>"><?= isset($item[$key]) ? $item[$key] : ''; ?></td>
									<?php endforeach; ?>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
						</tbody>
					<?php endif; ?>
				</table>

			</div>

			<input type="submit" name="generate" value="Download CSV"/>
		</form>

		<script>

			jQuery(document).ready(function ($) {

				$('.click-hide').click(function () {

					var cl = $(this).closest('th').attr('class');
					var input = $(this).closest('th').find('input').prop('checked', false);
					console.log(cl);
					$('.' + cl).fadeOut();
				});
			});
		</script>
	</div>

	<?php
}