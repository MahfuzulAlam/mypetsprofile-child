<?php

/**
 * RENTSYNC CONATCT UNITS
 */

?>
<div class="directorist-single-info directorist-single-contact-info">
    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><i class="las la-phone"></i></span>
        <span class="directorist-single-info__label--text">Phone</span>
    </div>
    <div class="contact_unit_owner directorist-single-info__value">
        <a href="tel:<?php echo $phone_formatted; ?>"><?php echo $phone; ?></a>
    </div>
    <?php if ($email && !empty($email)) : ?>
        <div class="directorist-single-info__label">
            <span class="directorist-single-info__label-icon"><i class="las la-envelope"></i></span>
            <span class="directorist-single-info__label--text">Email</span>
        </div>
        <div class="contact_unit_owner directorist-single-info__value">
            <a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a>
        </div>
    <?php endif; ?>
</div>