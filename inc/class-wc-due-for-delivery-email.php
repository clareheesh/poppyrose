<?php

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * A custom Expedited Order WooCommerce Email class
 *
 * @since 0.1
 * @extends \WC_Email
 */
class WC_Due_For_Delivery_Email extends WC_Email
{
	/**
	 * Set email defaults
	 *
	 * @since 0.1
	 */
	public function __construct()
	{

		// set ID, this simply needs to be a unique name
		$this->id = 'wc_due_for_delivery';

		// this is the title in WooCommerce Email settings
		$this->title = 'Due for delivery';

		// this is the description in WooCommerce email settings
		$this->description = 'Due for delivery notification emails are sent when an orders status is changed to "due for delivery"';
		$this->customer_email = true;

		// these are the default heading and subject lines that can be overridden using the settings
		$this->heading = 'Due for delivery';
		$this->subject = 'Due for delivery';

		// these define the locations of the templates that this email should use, we'll just use the new order template since this email is similar
		$this->template_html = 'emails/customer-due-for-delivery.php';
		$this->template_plain = 'emails/plain/customer-due-for-delivery.php';

		// Triggers for this email
		add_action( 'woocommerce_order_status_due-for-delivery', array( $this, 'trigger' ) );

		// Call parent constructor to load any other defaults not explicity defined here
		parent::__construct();
	}


	/**
	 * Determine if the email should actually be sent and setup email merge variables
	 *
	 * @since 0.1
	 * @param int $order_id
	 */
	public function trigger( $order_id ) {

		// bail if no order ID is present
		if ( ! $order_id )
			return;

		// setup order object
		$this->object = new WC_Order( $order_id );
		$this->recipient    = $this->object->billing_email;

		// bail if shipping method is not expedited
//		if ( ! $this->object->has_status('wc-due-for-delivery' ) )
//			return;


		$this->find['order-date']      = '{order_date}';
		$this->find['order-number']    = '{order_number}';

		$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $this->object->order_date ) );
		$this->replace['order-number'] = $this->object->get_order_number();


		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * Get content html.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_html() {
		return wc_get_template_html( $this->template_html, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => false,
			'email'			=> $this
		) );
	}

	/**
	 * Get content plain.
	 *
	 * @access public
	 * @return string
	 */
	public function get_content_plain() {
		return wc_get_template_html( $this->template_plain, array(
			'order'         => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => false,
			'plain_text'    => true,
			'email'			=> $this
		) );
	}

} // end \WC_Expedited_Order_Email class