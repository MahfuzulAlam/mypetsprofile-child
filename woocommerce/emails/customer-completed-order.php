<?php

/**
 * Customer completed order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-completed-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

if (!defined('ABSPATH')) {
	exit;
}

$iap_order = $order->get_billing_address_2() == 'IAP' ? true : false;
$product_ids = array();

/*
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action('woocommerce_email_header', $email_heading, $email); ?>

<?php /* translators: %s: Customer first name */ ?>
<p><?php printf(esc_html__('Hi %s,', 'woocommerce'), esc_html($order->get_billing_first_name())); ?></p>

<?php
foreach ($order->get_items() as $item_id => $item) {
	$product_ids[] = $item->get_product_id();
}


if (in_array(18053, $product_ids)) :
?>
	<div>
		<p>Welcome, and thank you for purchasing a MyPetsProfile™️ Elite Membership Plan.</p>

		<p>You now have all access to meet and greet neighbouring pet parents and local and national pet-friendly biz and services.</p>

		<p>Most importantly, you’ve chosen to help subsidize your rising pet costs. You now earn 10% commissions on your referrals that purchase MPP Elite Memberships, and Pet-friendly Biz and Event plans.</p>

		<p>Please review the attached link/form (<a href="<?php echo MPP_SITE_URL . '/affiliate-area/'; ?>">MPP Elite Dashboard</a>) and complete your information to receive commissions on your referral’s purchases.</p>

		<p>Commissions: We calculate commissions on every calendar month. And pay in the 20th of the following month - minimum payouts are $50.</p>

		<p>At anytime, go to the “Elite Members Portal” to review your earnings, post in the MPP Funnies Contests for rewards and more…</p>

		<p>We love seeing pet photos. Post as many as you like and share.</p>

		<p>Enjoy your MyPetsProfile™️ Neighborhood.</p>

		<p>Thank you</p>
		<p>The MyPetsProfile™️ Team</p>
	</div>

	<?php

else :

	$plan_id = mpp_get_pricing_plan_from_the_order($order->get_id());
	$purchase_form = '';
	if (WC_Product_Factory::get_product_type($plan_id) == 'listing_pricing_plans') {
		if ($plan_id == 18242) {
			$purchase_form = MPP_SITE_URL . '/add-edit-pet-friendly-event';
		} else {
			$purchase_form = MPP_SITE_URL . '/add-listing/?directory_type=' . default_directory_type() . '&plan=' . $plan_id;
		}
	?>
		<div>
			<p>Thank you for purchasing a MyPetsProfile™️ Neighborhood Biz Plan, with a Biz Profile and Group.</p>

			<p>You now have the opportunity to meet local pet parents and invite them to your pet-friendly residential community, business service or venue.</p>

			<?php if (!empty($purchase_form)) { ?><p>Please review the attached link/form ( <a href="<?php echo $purchase_form; ?>">Biz Entry Form</a> ) and complete the outstanding pet-friendly features and benefits of your Biz or Service offering.</p><?php } ?>

			<p>Your Social Biz Group is already created and linked to your biz profile.</p>

			<p>Click the Social link and update your biz info, photos and more to your group.</p>

			<p>The rest is easy. Start meeting local pet parents and invite them to your group and biz. Create valued posts, blogs discussions and more…</p>

			<p>Enjoy your MyPetsProfile™️ Neighborhood.</p>

			<p>Thank you</p>
			<p>The MyPetsProfile™️ Team</p>
		</div>
		<?php
	} else {
		if ($iap_order) {
		?>
			<p><?php esc_html_e('We have finished processing your order.', 'woocommerce'); ?></p>
	<?php
		}
	}
endif;
if (!$iap_order) :
	?>

	<p><?php esc_html_e('We have finished processing your order.', 'woocommerce'); ?></p>
<?php

	/*
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
	do_action('woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email);

	/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
	do_action('woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email);

	/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
	do_action('woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email);

	/**
	 * Show user-defined additional content - this is set in each email's settings.
	 */
	if ($additional_content) {
		echo wp_kses_post(wpautop(wptexturize($additional_content)));
	}

	/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
	do_action('woocommerce_email_footer', $email);

endif;
