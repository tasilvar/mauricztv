<?php

use bpmj\wpidea\Info_Message;
use bpmj\wpidea\modules\affiliate_program\api\dto\Commission_DTO_Collection;
use bpmj\wpidea\translator\Interface_Translator;

/* @var Interface_Translator $translator */
/* @var Commission_DTO_Collection $commissions */


if (!$commissions->valid()) {
    $message = new Info_Message($translator->translate('user_account.my_commissions.info'));
    $message->render();
} else {
    ?>
    <table class="my-commissions-table">
        <thead>
        <tr>
            <th><?= $translator->translate('user_account.my_commissions.id') ?></th>
            <th><?= $translator->translate('user_account.my_commissions.campaign') ?></th>
            <th><?= $translator->translate('user_account.my_commissions.sales_amount') ?></th>
            <th><?= $translator->translate('user_account.my_commissions.commission_amount') ?></th>
            <th><?= $translator->translate('user_account.my_commissions.sale_date') ?></th>
            <th><?= $translator->translate('user_account.my_commissions.status') ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($commissions as $commission) {
            $campaign = $commission->get_campaign();
            $campaign = !empty($campaign) ? $campaign : '-';
            echo '
          <tr>
             <td>#' . $commission->get_id() . '</td>
             <td>' . $campaign . '</td>
             <td>' . $commission->get_sales_amount() . '</td>
             <td>' . $commission->get_commission_amount() . '</td>
             <td>' . $commission->get_sale_date() . '</td>
             <td>' . $commission->get_status() . '</td>
          </tr>
        ';
        }
        ?>
        </tbody>
    </table>
    <?php
}
?>