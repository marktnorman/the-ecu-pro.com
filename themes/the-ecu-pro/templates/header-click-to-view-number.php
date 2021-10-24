<?php
// ctvn prefix = click to view number
global $tecup_form_show_number;
?>
<a href="#" class="phone-toggle d-lg-none text-white" data-js-ref="ctvnf-open"><i class="fas fa-phone"></i><span class="sr-only">Phone</span></a>
<div class="header-contact">
    <span>
        CALL US NOW<br>
        <?php if ($tecup_form_show_number) { ?>
          <b>+1-888-723-2080</b>
        <?php } else { ?>
          <b data-js-ref="ctvnf-number">+1-888-723-<a class="text-white" href="#" data-js-ref="ctvnf-open">CLICK</a></b>
          <span class="ctvnf-click-msg" data-js-ref="ctvnf-click-msg">to show number</span>
        <?php } ?>
    </span>
</div>
