<?php WPI()->templates->header(); ?>
<?php
/*
 * Load the content
 */
?>

<!-- Sekcja pod paskiem z menu -->
<div id="content" class="<?php echo apply_filters( 'bpmj_eddcm_template_section_css_class', 'content' ); ?>">
    <div class="contenter">
        <br><br>
        <?php
            $message = new bpmj\wpidea\Info_Message( 
                __( 'Unfortunately, you cannot display templates pages directly.', BPMJ_EDDCM_DOMAIN ),
                sprintf( __( 'Get back on the right track with %s!', BPMJ_EDDCM_DOMAIN ), '<a href="https://publigo.pl/?utm_source=web&utm_campaign=wpidea_404">Publigo</a>' ),
                'admin-appearance'
            );
            $message->render();
        ?>
    </div>
<!-- Koniec sekcji pod paskiem z menu -->
</div>

<?php WPI()->templates->footer(); ?>