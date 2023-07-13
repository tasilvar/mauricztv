<?php

use bpmj\wpidea\modules\captcha\api\Captcha_API_Static_Helper;
use bpmj\wpidea\helpers\Translator_Static_Helper;

WPI()->templates->header();
?>

    <div id="content">
        <div class="contenter">
            <h1><?php
                the_title(); ?></h1>
            <?php
            WPI()->templates->breadcrumbs(); ?>

            <?php
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    the_content();
                }
            }
            ?>
            <!-- Formularz -->
            <div class="form-wrapper">
                <?php
                if ('POST' === $_SERVER['REQUEST_METHOD']) {
                    if (is_email($_POST['email'])) {
                        $captcha = !empty($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';

                        if (!Captcha_API_Static_Helper::is_captcha_valid($captcha)) {
                            echo Translator_Static_Helper::translate('contact_form.captcha.spam_detected');
                        } else {
                            $headers = array('Content-Type: text/html; charset=UTF-8');

                            $message = __('New message from WP Idea', BPMJ_EDDCM_DOMAIN) . '<br><br>';
                            $message .= sprintf(__('Name: %s<br>', BPMJ_EDDCM_DOMAIN), sanitize_text_field($_POST['author']));
                            $message .= sprintf(__('E-mail: %s<br><br>', BPMJ_EDDCM_DOMAIN), sanitize_email($_POST['email']));
                            $message .= sprintf(__('Message: <br>%s', BPMJ_EDDCM_DOMAIN), nl2br(esc_textarea($_POST['message'])));

                            wp_mail(WPI()->templates->get_contact_email(), __('New message from WP Idea', BPMJ_EDDCM_DOMAIN), $message, $headers);
                            _e('Your email has been sent', BPMJ_EDDCM_DOMAIN);
                        }
                    }
                }
                ?>
                <br><br>
                <form action="" method="post" id="contact_form">
                    <input type="text" name="author" placeholder="<?php
                    _e('Name', BPMJ_EDDCM_DOMAIN); ?> *" required>
                    <input type="email" name="email" placeholder="E-mail *" required>
                    <textarea name="message" placeholder="<?php
                    _e('Message', BPMJ_EDDCM_DOMAIN); ?> *"></textarea>
                    <p>* <?php
                        _e('required fields', BPMJ_EDDCM_DOMAIN); ?></p>
                    <input class="kontakt_submit" id="contact_form_submit_button" type="submit" value="<?php
                    _e('Send', BPMJ_EDDCM_DOMAIN); ?>">
                </form>

            </div>
            <!-- Koniec formularza -->
        </div>
    </div>
<?php
WPI()->templates->footer(); ?>