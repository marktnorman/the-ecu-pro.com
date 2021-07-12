<?php // ctvn prefix = click to view number ?>
<a href="#" class="phone-toggle d-lg-none text-white" data-js-ref="ctvnf-click-to-show"><i
            class="fas fa-phone"></i><span class="sr-only">Phone</span></a>
<?php
global $tecup_form_error;
$which_form = 'ctvnf-form';
$show_ctvnf_form_with_error = $tecup_form_error && isset($_POST['which-form']) && $_POST['which-form'] === $which_form;
$show_number = isset($_GET['show-number']) && $_GET['show-number'] == 'true';
?>
<div id="ctvnf-form-cont-sm" class="ctvnf-form-cont text-center d-lg-none <?= $show_number ? 'is-open' : ''; ?>"
     data-js-ref="ctvnf-form-cont-sm">
    <a href="tel:+18887232080"><b class="headline">Phone: +1-888-723-2080</b></a>
</div>
<div class="header-contact">
    <span>
        CALL US NOW<br>
        <?php if (isset($_GET['show-number']) && $_GET['show-number'] == 'true') { ?>
            <b>+1-888-723-2080</b>
        <?php } else { ?>
            <b data-js-ref="ctvnf-number">+1-888-723-<a class="text-white" href="#" data-js-ref="ctvnf-click-to-show">CLICK</a></b>
            <span class="ctvnf-click-msg" data-js-ref="ctvnf-click-msg">to show number</span>
        <?php } ?>
    </span>
</div>
<div id="ctvnf-form-cont" class="ctvnf-form-cont text-left <?= $show_ctvnf_form_with_error ? 'is-open' : ''; ?>"
     data-js-ref="ctvnf-form-cont">
    <?php if ($show_number) { ?>
        <script>
            document.querySelector('[data-js-ref="ctvnf-number"]').innerText = '+1-888-723-2080'
            document.querySelector('[data-js-ref="ctvnf-click-msg"]').remove()
        </script>
    <?php } else {
    formMsg($which_form) ?>
        <form id="<?= $which_form ?>" name="form" class="label-placeholder-form" data-js-ref="lap-form" method="post">
            <input type="hidden" name="redirect_url" value="<?= get_the_permalink() ?>?show-number=true">
            <input type="hidden" name="which-form" value="<?= $which_form ?>">
            <input type="hidden" name="tecup-action" value="save_click_to_call">
            <h2 class="headline text-center">In case we miss your call</h2>
            <p class="intro text-center">We will call you back if we miss your call</p>
            <div class="field <?php field_error_class($tecup_form_error, $which_form, 'first', 'required'); ?>">
                <label id="ctvnf-label-fname" for="ctvnf-fname">First name</label>
                <input id="ctvnf-fname" type="text" maxlength="255" name="first" data-js-ref="lap-form-input"
                       data-js-label-id="ctvnf-label-fname" value="<?= $_POST['first'] ?? '' ?>">
            </div>
            <div class="field">
                <label id="ctvnf-label-lname" for="ctvnf-lname">Last name</label>
                <input id="ctvnf-lname" type="text" maxlength="255" name="last" data-js-ref="lap-form-input"
                       data-js-label-id="ctvnf-label-lname" value="<?= $_POST['last'] ?? '' ?>">
            </div>
            <div class="field">
                <label id="ctvnf-label-phone" for="ctvnf-phone">Phone</label>
                <input id="ctvnf-phone" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-phone" type="text"
                       name="phone" value="<?= $_POST['phone'] ?? '' ?>">
            </div>
            <div class="field  <?php field_error_class($tecup_form_error, $which_form, 'email', 'email'); ?>">
                <label id="ctvnf-label-email" for="ctvnf-email">Email</label>
                <input id="ctvnf-email" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-email" type="email"
                       maxlength="255" name="email" value="<?= $_POST['email'] ?? '' ?>">
            </div>
            <div class="field">
                <button type="submit" class="button text-center" data-js-ref="lap-form-submit">Show phone number
                </button>
            </div>
            <div class="field">
                <a href="#" data-js-ref="ctvnf-skip" class="ctvnf-skip text-center">Skip and show number</a>
            </div>
        </form>
    <?php
    // Including lapForm.js here because the load and execution time of site is so slow that there can be a long delay
    // between form being rendered and script executing if it is included in scripts.js spaghetti blob
    ?>
        <script>let lapScope = '[data-js-ref="ctvnf-form-cont"]'</script>
        <script src="<?= get_template_directory_uri() ?>/js/lapForm.js"></script>
    <?php } ?>
</div>
<script>
    const ctvnfFormCont = document.querySelector('[data-js-ref="ctvnf-form-cont"]')
    const ctvnfFormContSm = document.querySelector('[data-js-ref="ctvnf-form-cont-sm"]')
    document.querySelectorAll('[data-js-ref="ctvnf-click-to-show"]').forEach(toggle => {
        toggle.addEventListener('click', e => {
            e.preventDefault()
            ctvnfFormCont.classList.add('is-open')
        })
    })
    document.querySelector('[data-js-ref="ctvnf-skip"]').addEventListener('click', e => {
        e.preventDefault()
        window.scrollTo(0, 0);
        ctvnfFormContSm.classList.add('is-open')
        ctvnfFormCont.classList.remove('is-open')
        document.querySelector('[data-js-ref="ctvnf-number"]').innerText = '+1-888-723-2080'
        document.querySelector('[data-js-ref="ctvnf-click-msg"]').remove()
    })
</script>
