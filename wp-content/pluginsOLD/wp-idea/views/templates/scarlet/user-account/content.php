<?php

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\modules\affiliate_program\api\dto\Commission_DTO_Collection;
use bpmj\wpidea\modules\affiliate_program\core\value_objects\Partner_Info;
use bpmj\wpidea\modules\opinions\core\collections\Product_To_Rate_Collection;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\view\Interface_View_Provider;

/* @var Interface_View_Provider $view */
/* @var Interface_Translator $translator */
/* @var bool $is_logged_in */
/* @var Interface_Settings $settings */
/* @var Partner_Info $partner_info */
/* @var array $external_landing_links */
/* @var Commission_DTO_Collection $partner_commissions */
/* @var bool $disabled_partner_tab */
/* @var bool $opinions_enabled */
/* @var Product_To_Rate_Collection $products_user_can_rate */
/* @var string $user_name */
/* @var string $opinions_rules_url */

$courses_functionality_enabled = $settings->get(Settings_Const::COURSES_ENABLED) ?? true;

$disabled = $courses_functionality_enabled ? false : true;
?>
<?php
if (!$is_logged_in): ?>
    <?= do_shortcode('[edd_profile_editor]') ?>

    <?php
    return; ?>
<?php
endif; ?>

<?= $view->get('../components/tabbed-view/tabbed-view', [
    'tabs' => [
        [
            'tab-name' => $translator->translate('user_account.account_settings'),
            'tab-info' => $translator->translate('user_account.account_settings.details'),
            'tab-content' => do_shortcode('[edd_profile_editor]'),
            'class' => 'account-settings',
            'tab-id' => 'account-settings',
            'disabled' => false
        ],
        [
            'tab-name' => $translator->translate('user_account.my_courses'),
            'tab-info' => $translator->translate('user_account.my_courses.details'),
            'tab-content' => do_shortcode('[edd_pc_accessible_courses]'),
            'class' => 'my-courses',
            'tab-id' => 'courses',
            'disabled' => $disabled
        ],
        [
            'tab-name' => $translator->translate('user_account.my_digital_products'),
            'tab-info' => $translator->translate('user_account.my_digital_products.details'),
            'tab-content' => do_shortcode('[download_history]'),
            'class' => 'my-digital-products',
            'tab-id' => 'digital-products',
            'disabled' => false
        ],
        [
            'tab-name' => $translator->translate('user_account.my_services'),
            'tab-info' => $translator->translate('user_account.my_services.details'),
            'tab-content' => do_shortcode('[edd_pc_purchased_products show_courses=0 show_digital_products=0]'),
            'class' => 'my-services',
            'tab-id' => 'services',
        ],
        [
            'tab-name' => $translator->translate('user_account.my_certificates'),
            'tab-info' => $translator->translate('user_account.my_certificates.details'),
            'tab-content' => $view->get('../certificates/my-certificates', [
                'translator' => $translator
            ]),
            'class' => 'my-certificates',
            'tab-id' => 'certificates',
            'disabled' => $disabled
        ],
        [
            'tab-name' => $translator->translate('user_account.orders'),
            'tab-info' => $translator->translate('user_account.orders.details'),
            'tab-content' => do_shortcode('[purchase_history]'),
            'class' => 'my-orders',
            'tab-id' => 'orders',
            'disabled' => false
        ],
        [
            'tab-name' => $translator->translate('user_account.partner_program'),
            'tab-info' => $translator->translate('user_account.partner_program.details'),
            'tab-content' => $view->get('../affiliate-program/my-partner-profile', [
                'translator' => $translator,
                'affiliate' => $partner_info,
                'external_landing_links' => $external_landing_links
            ]),
            'class' => 'my-program',
            'tab-id' => 'partner-program',
            'disabled' => $disabled_partner_tab
        ],
        [
            'tab-name' => $translator->translate('user_account.affiliate_program.commissions'),
            'tab-info' => '',
            'tab-content' => $view->get('../affiliate-program/my-commissions', [
                'translator' => $translator,
                'commissions' => $partner_commissions
            ]),
            'class' => 'my-commissions',
            'tab-id' => 'commissions',
            'disabled' => $disabled_partner_tab
        ],
        [
            'tab-name' => $translator->translate('user_account.opinions'),
            'tab-info' => $translator->translate('user_account.opinions.details'),
            'tab-content' => $view->get('../opinions/add', [
                'translator' => $translator,
                'products_user_can_rate' => $products_user_can_rate,
                'user_name' => $user_name,
                'opinions_rules_url' => $opinions_rules_url,
            ]),
            'class' => 'opinions',
            'tab-id' => 'opinions',
            'disabled' => !$opinions_enabled,
        ],
    ]
]) ?>
