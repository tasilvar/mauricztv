<?php
use bpmj\wpidea\admin\Certificates_Table;

$status = get_option('bpmj_eddcm_license_status');

$table = new Certificates_Table();
$table->prepare_items();
?>
<div class="edd-courses-manager">
    <div class="wrap">
        <div class="row">
            <div class="heading animated fadeInDown">
                <?php _e( 'Users certificates', BPMJ_EDDCM_DOMAIN ); ?>
            </div>
        </div>
    </div>

    <form method="post">
        <div class="wrap">
            <?php $table->display(); ?>
        </div>
    </form>
</div>
