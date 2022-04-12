<?php

/**
 * WooCommerce Memberships
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Memberships to newer
 * versions in the future. If you wish to customize WooCommerce Memberships for your
 * needs please refer to https://docs.woocommerce.com/document/woocommerce-memberships/ for more information.
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2014-2021, SkyVerge, Inc. (info@skyverge.com)
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined('ABSPATH') or exit;

/**
 * Membership note email.
 *
 * @type string $email_heading email heading
 * @type string $membership_note membership note
 * @type \WC_Memberships_User_Membership $user_membership user membership
 * @type \WC_Memberships_User_Membership_Email $email the email object
 *
 * @version 1.12.4
 * @since 1.0.0
 */
?>

<?php do_action('woocommerce_email_header', $email_heading, $email); ?>

<?php if ($user_membership) : ?>

    <p><?php esc_html_e('Hello, a note has just been added to your membership:', 'woocommerce-memberships'); ?></p>

    <blockquote><?php echo wpautop(wptexturize($membership_note)) ?></blockquote>

<?php endif; ?>

<?php do_action('woocommerce_email_footer', $email);
