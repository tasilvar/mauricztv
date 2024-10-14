<?php
/** @var $section */
?>
<form class="wptao-tools-form" method="post" action="<?php echo admin_url('admin.php?page=wp-idea-tools&tab_import_students'); ?>">
    <?php wp_nonce_field('import_students'); ?>
    <input type="hidden" name="wp_idea[tools][import_students]">
    <h2><?php _e( 'Import students', BPMJ_EDDCM_DOMAIN ); ?></h2>
    <table class="form-table">
        <tbody>
        <tr>
            <?php
            $import_students_file_field = WPI()->tools->get_section_field( 'import_students', 'file' );
            ?>
            <th scope="row"><?php echo $import_students_file_field['title']; ?></th>
            <td>
                <?php WPI()->tools->display_section_field( 'import_students', $import_students_file_field['id'] ); ?>
                <p><?php _e( 'A CSV file is a comma-separated data file. The first record is the e-mail address, the second is the name and the third is the surname. <br>E.g. john@example.com,John,Doe', BPMJ_EDDCM_DOMAIN ); ?></p>
            </td>
        </tr>
        <tr>
            <?php
            $import_students_courses_field = WPI()->tools->get_section_field( 'import_students', 'courses' );

            $query = new WP_Query( array(
                'post_status' => 'publish',
                'post_type'   => 'courses',
				'posts_per_page' => -1
            ) );

            $courses_options = array();
            while ( $query->have_posts() ) {
                $query->the_post();
                $product_id = get_post_meta( get_the_ID(), 'product_id', true );
                $variable_prices = get_post_meta( get_the_ID(), 'variable_pricing', true );
                if ( 1 == $variable_prices ) {
                    $prices = get_post_meta( $product_id, 'edd_variable_prices', true );
                    foreach ( $prices as $price_id => $price )
                        $courses_options[ $product_id . '-' . $price_id ] = get_the_title() . ' - ' . $price['name'];
                } else {
                    $courses_options[ $product_id ] = get_the_title();
                }
            }
            ?>
            <th scope="row"><?php echo $import_students_courses_field['title']; ?></th>
            <td>
                <?php WPI()->tools->display_section_field( 'import_students', $import_students_courses_field['id'], array(
                    'options' => $courses_options,
                ) ); ?>
            </td>
        </tr>
        <tr>
            <?php
            $import_students_courses_field = WPI()->tools->get_section_field( 'import_students', 'access' );
            ?>
            <th scope="row"><?php echo $import_students_courses_field['title']; ?></th>
            <td>
                <?php WPI()->tools->display_section_field( 'import_students', $import_students_courses_field['id'] ); ?>
            </td>
        </tr>
        <tr>
            <?php
            $import_students_courses_field = WPI()->tools->get_section_field( 'import_students', 'notification' );
            ?>
            <th scope="row"><?php echo $import_students_courses_field['title']; ?></th>
            <td>
                <?php WPI()->tools->display_section_field( 'import_students', $import_students_courses_field['id'] ); ?>
                <p><?php _e( 'If you do not choose any course, this notification will not be sent', BPMJ_EDDCM_DOMAIN ); ?></p>
            </td>
        </tr>
        <tr>
            <?php
            $import_students_courses_field = WPI()->tools->get_section_field( 'import_students', 'mailing' );
            ?>
            <th scope="row"><?php echo $import_students_courses_field['title']; ?></th>
            <td>
                <?php
//                var_dump(WPI()->diagnostic->get_system_content( 'mailers' ));
                ?>
                <?php WPI()->tools->display_section_field( 'import_students', $import_students_courses_field['id'] ); ?>
                <p><?php _e( 'If you do not choose any course, this notification will not be sent', BPMJ_EDDCM_DOMAIN ); ?></p>
            </td>
        </tr>
        </tbody>
    </table>
    <p>
        <input type="submit" class="button button-primary" value="<?php _e( 'Import students', BPMJ_EDDCM_DOMAIN ); ?>">
    </p>
</form>
<script type="text/javascript">
    jQuery( document ).ready( function ( $ ) {
        $( '.wp_idea-browse' ).on( 'click', function( e ) {
            e.preventDefault();

            var self = $( this );

            // Create the media frame.
            var file_frame = wp.media.frames.file_frame = wp.media( {
                title: self.data( 'uploader_title' ),
                button: {
                    text: self.data( 'uploader_button_text' ),
                },
                multiple: false,
                library: {
                    type: 'text/csv'
                },
            } );

            file_frame.on( 'select', function () {
                var attachment = file_frame.state().get( 'selection' ).first().toJSON();

                self.prev( '.wp_idea-browse-url' ).val( attachment.url );
            } );

            // Finally, open the modal
            file_frame.open();
        } );
    } );
</script>
