<?php
/** @var \bpmj\wpidea\view\Interface_View_Provider $view */

use bpmj\wpidea\admin\menu\Admin_Menu_Item_Slug;

?>

<form class='edd-courses-manager-creator-steps-form'
    data-mode='digital_product'
    data-see-item-list-button-url="<?= admin_url('admin.php?page=' . Admin_Menu_Item_Slug::DIGITAL_PRODUCTS) ?>"
    data-edit-item-button-label="<?= __('Edit digital product', BPMJ_EDDCM_DOMAIN) ?>"
    data-saving-in-progress-message="<h3><?= __('Your digital product is being saved...', BPMJ_EDDCM_DOMAIN) ?></h3>"
    data-saving-in-progress-popup-title="<?= __('Saving digital product', BPMJ_EDDCM_DOMAIN) ?>"
    data-saving-complete-message="<h3><?= __('Your digital product has been saved!', BPMJ_EDDCM_DOMAIN) ?></h3>"
>

    <?= $view->get('steps/step-one') ?>
    <?= $view->get('steps/step-two') ?>

</form>