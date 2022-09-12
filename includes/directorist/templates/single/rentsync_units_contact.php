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
        <?php echo $phone; ?>
        <!-- <a href="tel:<?php echo $phone_formatted; ?>" target="_blank"><?php echo $phone; ?></a> -->
    </div>
</div>
<?php if ($email && !empty($email)) : ?>
    <div class="directorist-single-info directorist-single-contact-info">
        <div class="directorist-single-info__label">
            <span class="directorist-single-info__label-icon"><i class="las la-envelope"></i></span>
            <span class="directorist-single-info__label--text">Email</span>
        </div>
        <div class="contact_unit_owner directorist-single-info__value">
            <?php echo $email; ?>
            <!-- <a href="mailto:<?php echo $email; ?>" target="_blank"><?php echo $email; ?></a> -->
        </div>
    </div>
<?php endif; ?>