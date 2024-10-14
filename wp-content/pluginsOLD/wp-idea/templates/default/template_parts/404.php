<?php
/*
 * 404 template
 */

use bpmj\wpidea\Info_Message;

?>

<div class="bpmj_edd_404_not_found">
    <?php
        $info_message = new Info_Message( 
            __( 'Hey, we think that you have deviated from the course, there is nothing here! :(', BPMJ_EDDCM_DOMAIN ),
            sprintf( __( 'Get back on the right track with %s!', BPMJ_EDDCM_DOMAIN ), '<a href="https://publigo.pl/?utm_source=web&utm_campaign=wpidea_404">Publigo</a>' )
        );
        $info_message->render();
    ?>
</div>