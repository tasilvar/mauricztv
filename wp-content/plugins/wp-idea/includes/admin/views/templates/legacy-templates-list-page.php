<?php
use bpmj\wpidea\templates_system\admin\renderers\Template_Groups_Page_Renderer;

/** @var \bpmj\wpidea\admin\tables\Enhanced_Table $table */
/** @var string $page_title */
/** @var string $template_groups_page_url */
/** @var \bpmj\wpidea\admin\helpers\html\Info_Box $info_box */
/** @var \bpmj\wpidea\templates_system\groups\Template_Group $template_group */
?>
<div class="wrap group-templates-list-page">
    <h1 class="wp-heading-inline"><?= $page_title ?></h1>

    <hr class="wp-header-end">

    <?= $info_box->get_html() ?>

    <a href="<?= $template_groups_page_url ?>" class="go-back-link"><?= __('Go back to the templates list', BPMJ_EDDCM_DOMAIN) ?></a>
    <h2 class="wpi-header"><?= __('You are editing items of the template', BPMJ_EDDCM_DOMAIN) ?>: <strong><?= $template_group->get_name() ?></strong></h2>

    <?= $table->render() ?>
</div>