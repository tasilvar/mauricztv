<?php
use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

/* @var bool $can_view_sensitive_data */
/* @var array $course */
/* @var string $title */
/* @var string $disable_auto_stats */

?>

<h2><?= $title ?></h2>

<?php

if (!$can_view_sensitive_data) {
    return;
}

if ( 'draft' === $course[ 'status' ] ) {
    return;
}

?>
<table class="participants-stats">
    <tr>
        <td>
            <?php esc_attr_e( 'Active (current) / Inactive / All', BPMJ_EDDCM_DOMAIN ) ?>
        </td>
        <td>
            <?php
            if ( empty( $disable_auto_stats ) || '1' !== $disable_auto_stats ) {
                $participants = WPI()->courses->get_course_participants( $course[ 'id' ] );
                if ( $participants[ 'all' ] ) {
                    ?>
                    <a title="<?php esc_attr_e( 'Active (current) / Inactive / All', BPMJ_EDDCM_DOMAIN ) ?>"
                       href="<?php echo admin_url( 'admin.php?page=' . Admin_Menu_Item_Slug::STUDENTS . '&filter_courses[]=' . $course[ 'id' ] ); ?>" target="_blank"><strong><?php echo $participants[ 'active' ]; ?></strong>
                        / <?php echo $participants[ 'inactive' ]; ?>
                        / <?php echo $participants[ 'all' ]; ?>
                    </a>
                    <?php
                } else {
                    echo '0 / 0 / 0';
                }
            } else {
                ?>
                <button
                        type="button"
                        class="btn-eddcm btn-eddcm-default"
                        data-action="load-users-stats"
                        data-course="<?php echo $course[ 'id' ]; ?>"
                        title="<?php esc_attr_e( 'Load users stats', BPMJ_EDDCM_DOMAIN ); ?>">
                    <span class="dashicons dashicons-image-rotate"></span>
                </button>
                <?php
            }
            ?>
        </td>
    </tr>
</table>

<style>
    table.participants-stats {
        border-collapse: collapse;
        width: 100%;
    }

    table.participants-stats td {
        padding: 10px 20px;
        font-size: 16px;
        border: 1px solid #bfbfbf;
    }
</style>
