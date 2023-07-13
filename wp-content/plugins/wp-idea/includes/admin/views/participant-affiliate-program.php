<?php

/** @var Interface_Translator $translator */
/** @var array $affiliates */

?>
<div class="edd-courses-manager" id="participant-affiliate-program">
    <section class="edd-courses-manager-dashboard">
        <div class="row">
            <div class="full-column">
                <div class="panel courses no-courses animated fadeInUp">
                    <div class="panel-heading">
                        <?= $translator->translate('affiliate_program.participants.page_title') ?>
                    </div>
                    <div class="panel-body no-padding">

                        <table>
                            <thead>
                              <tr>
                                  <th><?= $translator->translate('affiliate_program.participants.id') ?></th>
                                  <th><?= $translator->translate('affiliate_program.participants.link') ?></th>
                                  <th><?= $translator->translate('affiliate_program.participants.status') ?></th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                             foreach($affiliates as $affiliate){
                                 echo'
                                <tr>
                                  <td>'.$affiliate['partner_id'].'</td>
                                  <td>'.$affiliate['link'].'</td>
                                  <td>'.$translator->translate('affiliate_program.participants.status.active').'</td>
                                </tr>';
                             }
                            ?>

                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


