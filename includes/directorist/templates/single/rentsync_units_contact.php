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
        </div>
    </div>
<?php endif; ?>