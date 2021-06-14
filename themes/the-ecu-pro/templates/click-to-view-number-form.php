<?php
// ctvn prefix = click to view number
global $tecup_form_error, $tecup_form_show_number;
$which_form                 = 'ctvnf-form';
$show_ctvnf_form_with_error = $tecup_form_error && isset($_POST['which-form']) && $_POST['which-form'] === $which_form;
?>
<div id="ctvnf-sm" class="ctvnf text-center p-3 d-lg-none <?= $tecup_form_show_number ? 'is-open' : ''; ?>"
     data-js-ref="ctvnf-sm">
    <div class="ctvnf-box p-3 px-5">
        <span class="ctvnf-close" data-js-ref="ctvnf-close"><img
                    src="<?= get_template_directory_uri() ?>/assets/images/x.svg" alt="close" class="img-fluid"><span
                    class="sr-only">Close</span></span>
        <a href="tel:+18887232080"><b class="headline">Phone: +1-888-723-2080</b></a>
    </div>
</div>
<div id="ctvnf" class="ctvnf text-left p-3 <?= $show_ctvnf_form_with_error ? 'is-open' : ''; ?>" data-js-ref="ctvnf">
    <div class="ctvnf-box p-3">
        <span class="ctvnf-close" data-js-ref="ctvnf-close"><img
                    src="<?= get_template_directory_uri() ?>/assets/images/x.svg" alt="close" class="img-fluid"><span
                    class="sr-only">Close</span></span>
        <?php formMsg($which_form, true) ?>
        <form id="<?= $which_form ?>" name="form" class="label-placeholder-form" data-js-ref="lap-form" method="post">
            <input type="hidden" name="which-form" value="<?= $which_form ?>">
            <input type="hidden" name="tecup-action" value="save_click_to_call">
            <h2 class="headline text-center px-3 pt-3">In case we miss your call</h2>
            <p class="intro text-center">We will call you back if we miss your call</p>
            <div class="field <?php field_error_class($tecup_form_error, $which_form, 'first', 'required'); ?>">
                <label id="ctvnf-label-fname" for="ctvnf-fname">Your name</label>
                <input id="ctvnf-fname" type="text" maxlength="255" name="cname" data-js-ref="lap-form-input"
                       data-js-label-id="ctvnf-label-fname" value="<?= $_POST['cname'] ?? '' ?>">
            </div>
            <div class="field">
                <label id="ctvnf-label-phone" for="ctvnf-phone">Your phone number</label>
                <input id="ctvnf-phone" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-phone" type="text"
                       name="phone" value="<?= $_POST['phone'] ?? '' ?>">
            </div>
            <div class="field  <?php field_error_class($tecup_form_error, $which_form, 'email', 'email'); ?>">
                <label id="ctvnf-label-email" for="ctvnf-email">Your email address</label>
                <input id="ctvnf-email" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-email" type="email"
                       maxlength="255" name="email" value="<?= $_POST['email'] ?? '' ?>">
            </div>
            <div class="field">
                <button type="submit" class="button lap-form-button text-center" data-js-ref="lap-form-submit">Show
                    phone number
                </button>
            </div>
            <div class="field">
                <a href="#" data-js-ref="ctvnf-skip" class="ctvnf-skip text-center">Skip and show number</a>
            </div>
        </form>
    </div>
</div>
<script>
    const ctvnfFormCont = document.querySelector('[data-js-ref="ctvnf"]')
    const ctvnfFormContSm = document.querySelector('[data-js-ref="ctvnf-sm"]')
    const ctvnfFormContSkip = document.querySelector('[data-js-ref="ctvnf-skip"]')
    const ctvnfOpen = document.querySelectorAll('[data-js-ref="ctvnf-open"]')
    const ctvnfClose = document.querySelectorAll('[data-js-ref="ctvnf-close"]')
    const ctvnfNumber = document.querySelectorAll('[data-js-ref="ctvnf-number"]')
    const ctvnfMsg = document.querySelectorAll('[data-js-ref="ctvnf-click-msg"]')

    // Retrieve from storage
    let form_status = localStorage.getItem('form-submitted');

    ctvnfOpen.forEach(toggle => {
        toggle.addEventListener('click', e => {
            e.preventDefault()

            if (!form_status) {
                ctvnfFormCont.classList.add('is-open')
            } else {
                jQuery(ctvnfFormContSkip).trigger("click");
            }
        })
    })
    if (ctvnfFormContSkip) {
        ctvnfFormContSkip.addEventListener('click', e => {
            e.preventDefault()
            ctvnfFormContSm.classList.add('is-open')
            ctvnfFormCont.classList.remove('is-open')
            ctvnfNumber.forEach(numberSlot => {
                numberSlot.innerText = '+1-888-723-2080'
            })
            ctvnfMsg.forEach(msgSlot => {
                msgSlot.remove()
            })
        })
    }
    ctvnfClose.forEach(close => {
        close.addEventListener('click', e => {
            e.preventDefault()
            ctvnfFormCont.classList.remove('is-open')
            ctvnfFormContSm.classList.remove('is-open')
        })
    })
</script>
