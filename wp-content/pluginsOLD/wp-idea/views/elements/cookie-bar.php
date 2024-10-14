<?php
use bpmj\wpidea\settings\LMS_Settings;
/** @var string $cookie_bar_content */
/** @var string $privacy_policy_page_url */
/** @var string $privacy_policy_page_title */
/** @var string $cookie_bar_button_text */
/** @var string $site_path */
?>

<div class="cookie-bar-container">
    <div class="cookie-bar">
        <div class="contenter">
            <div class="content-container">
                <?= $cookie_bar_content; ?>
                <div>
                    <?php
                    if ( ! empty( $privacy_policy_page_url ) ) : ?>
                        <a href="<?= $privacy_policy_page_url; ?>"><?= $privacy_policy_page_title; ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="button-container">
                <a href="#" class="button"><?= $cookie_bar_button_text; ?></a>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery( document ).ready( function ( $ ) {
        var cookie_bar_container_element = $( '.cookie-bar-container' );

        var hide_bar_cookie = getCookie('hide_cookie_bar');
        if ( hide_bar_cookie === undefined || hide_bar_cookie === "false" ) {
            cookie_bar_container_element.show();

            $('.cookie-bar .button-container a').on( 'click', function ( e ) {
                e.preventDefault();

                cookie_bar_container_element.slideUp();

                var expires = new Date(),
                    expireDays = 365 * 10;

                expires.setDate(expires.getDate() + expireDays);
                document.cookie = 'hide_cookie_bar=true; path=<?= $site_path ?>; expires=' + expires.toUTCString();
            } );
        }
    } );
</script>

