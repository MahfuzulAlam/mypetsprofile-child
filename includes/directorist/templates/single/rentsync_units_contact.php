<?php

/**
 * RENTSYNC CONATCT UNITS
 */

?>
<div>
    <div class="directorist-single-info__label">
        <span class="directorist-single-info__label-icon"><i class="las la-phone"></i></span>
        <span class="directorist-single-info__label--text">Contact</span>
    </div>
    <div class="contact_unit_owner">
        <!-- <a href="tel:<?php //echo $phone; 
                            ?>" class="button"><i class="las la-phone"></i> Call Now</a>
        <a href="https://wa.me/<?php //echo $phone; 
                                ?>" class="button"><i class="lab la-whatsapp"></i> Whatsapp</a> -->
        <a href="#" class="button" onclick="window.open('tel:<?php echo $phone; ?>');"><i class="las la-phone"></i> Call Now</a>
        <a href="mailto:<?php echo $email; ?>" class="button" target="_blank"><i class="las la-envelope"></i> Email</a>
    </div>
</div>