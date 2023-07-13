<?php

namespace bpmj\wpidea\modules\increasing_sales;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\modules\increasing_sales\api\{Increasing_Sales_API, Increasing_Sales_API_Static_Helper};
use bpmj\wpidea\modules\increasing_sales\api\controllers\{Admin_Increasing_Sales_Ajax_Controller,
    Admin_Increasing_Sales_Controller,
    Increasing_Sales_Ajax_Controller
};
use bpmj\wpidea\modules\increasing_sales\core\services\Discount_Applier;
use bpmj\wpidea\modules\increasing_sales\core\services\Offer_Type_To_Payment_Assigner;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\increasing_sales\core\services\Active_Offer_Provider;

class Increasing_Sales_Module implements Interface_Module
{
    public const COOKIE_LIFE_TIME = 60 * 60 * 24 * 30;
    public const COOKIE_NAME = 'publigo_cart_oid';

    private Increasing_Sales_API $increasing_sales_api;
    private Offer_Type_To_Payment_Assigner $offer_type_to_payment_assigner;
    private Discount_Applier $discount_applier;
    private Interface_Actions $actions;
    private Subscription $subscription;
    private Interface_Settings $settings;
    private Active_Offer_Provider $active_offer_provider;

    public function __construct(
        Increasing_Sales_API $increasing_sales_api,
        Interface_Settings $settings,
        Subscription $subscription,
        Discount_Applier $discount_applier,
        Interface_Actions $actions,
        Offer_Type_To_Payment_Assigner $offer_type_to_payment_assigner,
        Active_Offer_Provider $active_offer_provider
    ) {
        $this->increasing_sales_api = $increasing_sales_api;
        $this->settings = $settings;
        $this->subscription = $subscription;
        $this->discount_applier = $discount_applier;
        $this->actions = $actions;
        $this->offer_type_to_payment_assigner = $offer_type_to_payment_assigner;
        $this->active_offer_provider = $active_offer_provider;
    }

    public function get_routes(): array
    {
        return [
            'increasing_sales_ajax' => Increasing_Sales_Ajax_Controller::class,
            'admin/increasing_sales_ajax' => Admin_Increasing_Sales_Ajax_Controller::class,
            'admin/increasing_sales' => Admin_Increasing_Sales_Controller::class
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'increasing_sales.menu_title' => 'Zwiększ sprzedaż',
                'increasing_sales.title' => 'Zwiększanie sprzedaży',

                'increasing_sales.event.upsell' => 'w zamian',
                'increasing_sales.event.bump' => 'dodatkowo',

                'increasing_sales.column.id' => 'ID',
                'increasing_sales.column.product' => 'Produkt w koszyku',
                'increasing_sales.column.offer_type' => 'Zaoferuj',
                'increasing_sales.column.offered_product' => 'Produkt zwiększający wartość',
                'increasing_sales.column.discount' => 'Rabat cenowy',

                'increasing_sales.actions.add_offer' => 'Dodaj nową ofertę',
                'increasing_sales.actions.edit' => 'Edytuj ofertę',
                'increasing_sales.actions.delete' => 'Usuń ofertę',

                'increasing_sales.actions.delete.loading' => 'Usuwam...',
                'increasing_sales.actions.delete.confirm' => 'Czy na pewno chcesz usunąć wybraną pozycje? Ta czynność jest nieodwracalna.',
                'increasing_sales.actions.delete.success' => 'Oferta została pomyślnie usunięta!',

                'increasing_sales.actions.delete.bulk' => 'Usuń oferty',

                'increasing_sales.actions.delete.bulk.confirm' => 'Czy na pewno chcesz usunąć wybrane pozycje? Ta czynność jest nieodwracalna.',
                'increasing_sales.actions.delete.bulk.success' => 'Oferty zostały pomyślnie usunięte!',

                'increasing_sales.form.select_option' => 'wybierz opcję',
                'increasing_sales.form.product' => 'Produkt w koszyku',
                'increasing_sales.form.offer_type' => 'Zaoferuj',
                'increasing_sales.form.offered_product' => 'Produkt zwiększający wartość sprzedaży',
                'increasing_sales.form.title' => 'Tytuł oferty',
                'increasing_sales.form.description' => 'Opis oferty',
                'increasing_sales.form.image' => 'Obrazek dla oferty',
                'increasing_sales.form.discount.warning' => 'Jeśli ustawiona poniżej wartość rabatu cenowego będzie równa lub wyższa od ceny regularnej danego produktu to wówczas w koszyku będzie ona wynosić 0,01 PLN.',
                'increasing_sales.form.discount' => 'Rabat cenowy (kwotowy)',

                'increasing_sales.form.choose_image' => 'wybierz',
                'increasing_sales.form.change_image' => 'Zmień obrazek',
                'increasing_sales.form.remove_image' => 'Usuń obrazek',
                'increasing_sales.form.no_image' => 'Aktualnie nie został ustawiony żaden obrazek.',

                'increasing_sales.form.add' => 'Dodaj nową ofertę',
                'increasing_sales.form.edit' => 'Edytuj ofertę',
                'increasing_sales.form.save' => 'Zapisz',
                'increasing_sales.form.while_saving' => 'Zapisuję...',
                'increasing_sales.form.cancel' => 'Anuluj',
                'increasing_sales.form.return' => 'Powrót',
                'increasing_sales.order.bump_title.also_buy' => 'Kup dodatkowo: ',
                'increasing_sales.order.upsell_title.buy' => 'Kup ',
                'increasing_sales.order.upsell_title.instead' => ' zamiast ',
            ],
            'en_US' => [
                'increasing_sales.menu_title' => 'Increase your sales',
                'increasing_sales.title' => 'Increasing sales',

                'increasing_sales.event.upsell' => 'in return',
                'increasing_sales.event.bump' => 'additionally',

                'increasing_sales.column.id' => 'ID',
                'increasing_sales.column.product' => 'Product in cart',
                'increasing_sales.column.offer_type' => 'Make an offer',
                'increasing_sales.column.offered_product' => 'Product that adds value',
                'increasing_sales.column.discount' => 'Price discount',

                'increasing_sales.actions.add_offer' => 'Add new offer',
                'increasing_sales.actions.edit' => 'Edit offer',
                'increasing_sales.actions.delete' => 'Delete offer',

                'increasing_sales.actions.delete.loading' => 'Removing ...',
                'increasing_sales.actions.delete.confirm' => 'Are you sure you want to delete the selected items? This action is irreversible.',
                'increasing_sales.actions.delete.success' => 'The offer has been successfully removed!',

                'increasing_sales.actions.delete.bulk' => 'Delete offers',

                'increasing_sales.actions.delete.bulk.confirm' => 'Are you sure you want to delete the selected items? This action is irreversible. ',
                'increasing_sales.actions.delete.bulk.success' => 'Offers have been successfully removed!',

                'increasing_sales.form.select_option' => 'select an option',
                'increasing_sales.form.product' => 'Product in cart',
                'increasing_sales.form.offer_type' => 'Make an offer',
                'increasing_sales.form.offered_product' => 'Product that adds value to sales',
                'increasing_sales.form.title' => 'Offer title',
                'increasing_sales.form.description' => 'Offer description',
                'increasing_sales.form.image' => 'Image for the offer',
                'increasing_sales.form.discount.warning' => 'If the discount value set below is equal to or higher than the regular price of a given product, then it will be PLN 0.01 in the basket.',
                'increasing_sales.form.discount' => 'Price (amount) discount',

                'increasing_sales.form.choose_image' => 'select',
                'increasing_sales.form.change_image' => 'Change picture',
                'increasing_sales.form.remove_image' => 'Remove Image',
                'increasing_sales.form.no_image' => 'Currently no image has been set.',

                'increasing_sales.form.add' => 'Add new offer',
                'increasing_sales.form.edit' => 'Edit offer',
                'increasing_sales.form.save' => 'Save',
                'increasing_sales.form.while_saving' => 'Saving ...',
                'increasing_sales.form.cancel' => 'Cancel',
                'increasing_sales.form.return' => 'Return',
                'increasing_sales.order.bump_title.also_buy' => 'Also buy: ',
                'increasing_sales.order.upsell_title.buy' => 'Buy ',
                'increasing_sales.order.upsell_title.instead' => ' instead ',
            ]
        ];
    }

    public function init(): void
    {
        Increasing_Sales_API_Static_Helper::init($this->increasing_sales_api);

        if ($this->is_active()) {
            $this->offer_type_to_payment_assigner->init();
            $this->actions->add(Action_Name::INIT, [$this->active_offer_provider, 'maybe_clean_invalid_cookie']);
            $this->actions->add(Action_Name::INIT, [$this->discount_applier, 'init']);
        }
    }

    private function is_active(): bool
    {
        return $this->subscription->get_plan() === Subscription_Const::PLAN_PRO &&
            $this->settings->get(Settings_Const::INCREASING_SALES_ENABLED);
    }
}