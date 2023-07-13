<?php

namespace bpmj\wpidea\admin;

use bpmj\wpidea\Info_Message;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_Info;
use bpmj\wpidea\translator\Interface_Translator;

/** @var Interface_Translator $translator */
/** @var Partner_Info $affiliate */
/** @var array $external_landing_links */

if (!$affiliate) : ?>
    <?php
    $message = new Info_Message($translator->translate('affiliate_program.participants.no_information'));
    $message->render();
    ?>
<?php
else : ?>
    <div class="partner-program" id="affiliate-partner-program">
        <section class="partner-program-dashboard">
            <div class="row">
                <div class="full-column">
                    <div class="panel-heading">
                        <?= $translator->translate('user_account.partner_program.title') ?>
                    </div>
                    <div class="panel-body no-padding">
                        <table id="info_partner">
                            <thead>
                            <tr>
                                <th><?= $translator->translate('affiliate_program.participants.id') ?></th>
                                <td> <?= $affiliate ? $affiliate->get_partner_id() : ""; ?> </td>
                            <tr>
                                <th><?= $translator->translate('affiliate_program.participants.link') ?></th>
                                <td> <?= $affiliate ? $affiliate->get_affiliate_link() : ""; ?> </td>
                            </tr>
                            <tr>
                                <th><?= $translator->translate('affiliate_program.participants.status') ?></th>
                                <td> <?= $translator->translate('affiliate_program.participants.status.active') ?> </td>
                            </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="panel-heading">
                        <?= $translator->translate('user_account.my_partner_profile.external_landing_links.title') ?>
                    </div>
                    <div class="panel-body no-padding">
                        <?php
                        if (!$external_landing_links) :
                            $message = new Info_Message($translator->translate('user_account.my_partner_profile.external_landing_links.info'));
                            $message->render();
                        else : ?>
                        <table id="info_partner">
                            <thead>
                            <tr>
                                <th><?= $translator->translate('user_account.my_partner_profile.external_landing_links.id') ?></th>
                                <th><?= $translator->translate('user_account.my_partner_profile.external_landing_links.product') ?></th>
                                <th><?= $translator->translate('user_account.my_partner_profile.external_landing_links.link') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($external_landing_links as $external_landing_link) {
                                echo '
                              <tr>
                                 <td>#' . $external_landing_link['id'] . '</td>
                                 <td>' . $external_landing_link['product'] . '</td>
                                 <td>' . $external_landing_link['landing_url'] . '</td>
                              </tr>
                              ';
                            }
                            ?>
                            </tbody>
                            <?php
                            endif; ?>
                        </table>
                    </div>
                    <div class="panel-heading">
                        <?= $translator->translate('user_account.my_partner_profile.campaign.title') ?>
                    </div>
                    <div class="panel-body no-padding">
                        <?= $translator->translate('user_account.my_partner_profile.campaign.info') ?>
                    </div>

                </div>
            </div>
        </section>
    </div>
<?php
endif; ?>