<?php
use bpmj\wpidea\admin\tables\dynamic\config\Dynamic_Table_Config;

/** @var Dynamic_Table_Config $config */
/** @var string $classes */

$json_config = $config->get_prepared_json();
?>
<div id='table-view-container' class="dynamic-table-view <?= $classes ?>">

</div>

<script>
    let tableConfig = JSON.parse('<?= $json_config ?>');
</script>
<script crossorigin src='<?= BPMJ_EDDCM_URL . '/assets/admin/js/table-view.js?ver=' . BPMJ_EDDCM_VERSION ?>'></script>

<?php
