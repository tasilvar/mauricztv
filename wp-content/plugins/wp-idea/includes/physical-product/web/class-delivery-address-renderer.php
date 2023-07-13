<?php

namespace bpmj\wpidea\physical_product\web;

use bpmj\wpidea\app\physical_products\Physical_Products_App_Service;
use bpmj\wpidea\events\actions\Action_Name;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\physical_product\model\Physical_Product_ID;
use bpmj\wpidea\translator\Interface_Translator;

class Delivery_Address_Renderer implements Interface_Initiable
{
    private Interface_Actions $actions;
    private Interface_Translator $translator;
    private Physical_Products_App_Service $physical_products_app_service;

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator,
        Physical_Products_App_Service $physical_products_app_service
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
        $this->physical_products_app_service = $physical_products_app_service;
    }

    public function init(): void
    {
        $this->actions->add(Action_Name::PURCHASE_FORM_AFTER_CC_FORM, [$this, 'render_delivery_address_html'], 1);
    }

    public function render_delivery_address_html(): void
    {
        if (!$this->physical_products_app_service->is_physical_product_in_the_cart()) {
            return;
        }
        ?>
        <fieldset id="edd_delivery_address">
            <span><legend><?= $this->translator->translate('physical_product_editor.cart.delivery_address.title') ?></legend></span>

            <p>
                <label class="edd-label required" for="bpmj-eddcm-delivery-address-first-name">
                    <?= $this->translator->translate('physical_product_editor.cart.delivery_address.first_name') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_first_name"
                       id="bpmj-eddcm-delivery-address-first-name"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.first_name') ?>"
                       value=""
                       required>
            </p>
            <p>
                <label class="edd-label required" for="bpmj-eddcm-delivery-address-last-name">
                    <?= $this->translator->translate('physical_product_editor.cart.delivery_address.last_name') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_last_name"
                       id="bpmj-eddcm-delivery-address-last-name"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.last_name') ?>"
                       value=""
                       required>
            </p>
            <p>
                <label class="edd-label" for="bpmj-eddcm-delivery-address-company">
                    <?= $this->translator->translate('physical_product_editor.cart.delivery_address.company') ?>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_company"
                       id="bpmj-eddcm-delivery-address-company"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.company') ?>"
                       value="">
            </p>

            <p>
                <label class="edd-label required" for="bpmj-eddcm-delivery-address-phone">
                    <?= $this->translator->translate('physical_product_editor.cart.delivery_address.phone') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_phone"
                       id="bpmj-eddcm-delivery-address-phone"
                       placeholder="+48600100200" value="" maxlength="14"
                       required>
            </p>
            <p>
                <label class="edd-label required" for="bpmj-eddcm-delivery-address-street">
                    <?= $this->translator->translate('physical_product_editor.cart.delivery_address.street') ?>
                    <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_street" id="bpmj-eddcm-delivery-address-street"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.street') ?>" value=""
                       required>
            </p>
            <p>
                <label class="edd-label required"
                       for="bpmj-eddcm-delivery-address-building-number"><?= $this->translator->translate(
                        'physical_product_editor.cart.delivery_address.building_number'
                    ) ?> <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" pattern="[0-9a-zA-Z]+" name="edd_delivery_address_building_number"
                       id="bpmj-eddcm-delivery-address-building-number"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.building_number') ?>" value="" required>
            </p>
            <p>
                <label class="edd-label"
                       for="bpmj-eddcm-delivery-address-apartment-number"><?= $this->translator->translate(
                        'physical_product_editor.cart.delivery_address.apartment_number'
                    ) ?>
                </label>
                <input class="edd-input required" type="text" pattern="[0-9a-zA-Z]+" name="edd_delivery_address_apartment_number"
                       id="bpmj-eddcm-delivery-address-apartment-number"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.apartment_number') ?>" value="">
            </p>
            <p>
                <label class="edd-label"
                       for="bpmj-eddcm-delivery-address-postal-code"><?= $this->translator->translate(
                        'physical_product_editor.cart.delivery_address.postal_code'
                    ) ?> <span class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input required" type="postal_code" pattern="^[0-9]{2}-[0-9]{3}$" name="edd_delivery_address_postal_code"
                       id="bpmj-eddcm-delivery-address-postal-code"
                       placeholder="00-000" value="" required>
            </p>
            <p>
                <label class="edd-label"
                       for="bpmj-eddcm-delivery-address-city"><?= $this->translator->translate('physical_product_editor.cart.delivery_address.city') ?> <span
                            class="edd-required-indicator">*</span>
                </label>
                <input class="edd-input" type="text" name="edd_delivery_address_city" id="bpmj-eddcm-delivery-address-city"
                       placeholder="<?= $this->translator->translate('physical_product_editor.cart.delivery_address.city') ?>" value=""
                       required>
            </p>
        </fieldset>
        <?php
    }
}