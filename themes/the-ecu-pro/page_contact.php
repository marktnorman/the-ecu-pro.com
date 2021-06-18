<?php
/**
 * Template Name: Contact us
 */

get_header('primary'); ?>

    <div id="main" class="column1 boxed no-breadcrumbs contact-us"><!-- main -->
        <div class="container">
            <div class="row main-content-wrap">

                <!-- main content -->
                <div class="main-content col-lg-12">
                    <div id="content" role="main">
                        <article class="page type-page status-publish">
                            <div class="page-content">

                                <h2 class="homepage-section-title">Get in touch</h2>
                                <div class="col-lg-12 top-section">
                                    <p>The ECU Pro offers professional automotive electronic repairs for BMW, Mini and
                                        Merc. We strive to provide our customers with an exceptional experience that
                                        cannot be beaten. With branches in 35 countries and more than 20 years of
                                        experience in automotive electronics we will do whatever it takes to get your
                                        vehicle back on the road, with the least inconvenience to you – our
                                        customer.</p>
                                </div>
                                <div class="col-lg-12 bottom-section">
                                    <div class="col-3">
                                        <h3>Contact us</h3>
                                        <ul>
                                            <li class="marker">
                                                <span class="icon location-icon"></span><span>181 Hills Creek Rd,<br/>Wellsboro, PA 16901 USA</span>
                                            </li>
                                            <li class="phone">
                                                <span class="icon phone-icon"></span><span>+1-888-723-2080</span>
                                            </li>
                                            <li class="mail">
                                                <span class="icon email-icon"></span><span>info@the-ecu-pro.com</span>
                                            </li>
                                            <li class="clock">
                                                <span class="icon clock-icon"></span><span>Monday - Friday 9am to 5pm<br/>Saturday - 9am to 2pm<br/>Sunday - Closed</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-5">
                                        <iframe style="border: 0;"
                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d40029.108957527795!2d-77.21940203475442!3d41.78130573651359!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89ce2b6cc62f9fff%3A0xd03412afaecb24c7!2s181+Hills+Creek+Rd%2C+Wellsboro%2C+PA+16901%2C+USA!5e0!3m2!1sen!2sbd!4v1562573053739!5m2!1sen!2sbd"
                                                width="100%" height="450" frameborder="0"
                                                allowfullscreen="allowfullscreen"></iframe>
                                    </div>
                                    <div class="col-4">
                                        <ul class="cta-container d-lg-none">
                                            <?php if (isOfficeOpen()) { ?>
                                                <li class="contact-button"><a href="tel:+18887232080" data-js-ref="ctvnf-open">Call us now</a></li>
                                                <li class="contact-button"><a class="message-button" href="#">Message</a></li>
                                            <?php } else { ?>
                                                <li class="contact-button single"><a class="message-button single" href="#">Message</a></li>
                                            <?php } ?>
                                        </ul>
                                        <?php
                                        global $tecup_form_error, $tecup_form_success;
                                        $which_form = 'contact-form';
                                        formMsg($which_form, false);
                                        ?>
                                        <div class="contact-form-container <?= $tecup_form_error ? 'd-block' : 'd-none d-lg-block' ?>" data-js-ref="contact-form-container">
                                          <form id="<?=$which_form?>" name="form" class="label-placeholder-form" data-js-ref="lap-form" method="post">
                                            <input type="hidden" name="which-form" value="<?=$which_form?>">
                                            <input type="hidden" name="tecup-action" value="save_contact_message">
                                            <div class="field <?php field_error_class($tecup_form_error, $which_form, 'cname', 'required'); ?>">
                                              <label id="contact-label-name" for="contact-name">Your name</label>
                                              <input id="contact-name" type="text" maxlength="255" name="cname" data-js-ref="lap-form-input" data-js-label-id="contact-label-name" value="<?=$_POST['cname'] ?? ''?>">
                                            </div>
                                            <div class="field <?php field_error_class($tecup_form_error, $which_form, 'email', 'email'); ?>">
                                              <label id="contact-label-email" for="contact-email">Your email</label>
                                              <input id="contact-email" data-js-ref="lap-form-input" data-js-label-id="contact-label-email" type="email" maxlength="255" name="email" value="<?=$_POST['email'] ?? ''?>">
                                            </div>
                                            <div class="field">
                                              <label id="ctvnf-label-phone" for="ctvnf-phone">Your phone number</label>
                                              <input id="ctvnf-phone" data-js-ref="lap-form-input" data-js-label-id="ctvnf-label-phone" type="text" name="phone" value="<?=$_POST['phone'] ?? ''?>">
                                            </div>
                                            <div class="field <?php field_error_class($tecup_form_error, $which_form, 'subject', 'required'); ?>">
                                              <label id="contact-label-vehicle" for="contact-vehicle">Make, Model, Engine, Year</label>
                                              <input id="contact-vehicle" type="text" maxlength="255" name="vehicle" data-js-ref="lap-form-input" data-js-label-id="contact-label-vehicle" value="<?=$_POST['vehicle'] ?? ''?>">
                                            </div>
                                            <div class="field">
                                              <textarea id="contact-msg" name="message" class="p-3 <?php field_error_class($tecup_form_error, $which_form, 'message', 'required'); ?>" placeholder="Message"><?=$_POST["message"] ?? ''?></textarea>
                                            </div>
                                            <div class="field">
                                              <button type="submit" class="button lap-form-button text-center" data-js-ref="lap-form-submit">Send message</button>
                                            </div>
                                          </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    </div>
                </div><!-- end main content -->

                <div class="sidebar-overlay"></div>

            </div>
        </div>
    </div>

<?php get_footer(); ?>
