<?php

namespace bpmj\wpidea\modules\gus_api;

use bpmj\wpidea\modules\gus_api\web\Button_Renderer;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use bpmj\wpidea\modules\gus_api\core\events\external\handlers\Initiate_Checkout_Handler;
use bpmj\wpidea\modules\gus_api\core\providers\Interface_Gus_API_Config_Provider;

class Gus_API_Module implements Interface_Module
{
    public const SEARCH_DATA_ENDPOINT = 'https://api.publigo.pl/gus/getByNip';

    private Initiate_Checkout_Handler $initiate_checkout_handler;
    private Button_Renderer $button_renderer;
    private Interface_Gus_API_Config_Provider $gus_api_config_provider;

    public function __construct(
        Initiate_Checkout_Handler $initiate_checkout_handler,
        Button_Renderer $button_renderer,
        Interface_Gus_API_Config_Provider $gus_api_config_provider
    ) {
        $this->initiate_checkout_handler = $initiate_checkout_handler;
        $this->button_renderer = $button_renderer;
        $this->gus_api_config_provider = $gus_api_config_provider;
    }

    public function init(): void
    {
        if (!$this->gus_api_config_provider->is_enabled()) {
            return;
        }

        $this->button_renderer->init();
        $this->initiate_checkout_handler->init();
    }

    public function get_routes(): array
    {
        return [];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'invoice_data.button.downloading' => 'Pobieram...',
                'invoice_data.button.download_from_gus' => 'Pobierz dane z GUS',
                'invoice_data.nip_field.error' => '<b>Uwaga:</b> Wprowadzony numer NIP jest nieprawidłowy lub dotyczy podmiotu spoza Polski.',
                'invoice_data.nip_field.data_empty' => '<b>Uwaga:</b> Nie znaleziono podmiotu dla podanego identyfikatora NIP.',
                'gus_api.error_processing_request' => '<b>Uwaga:</b> Podczas przetwarzania żądania wystąpił błąd.'
            ],
            'en_US' => [
                'invoice_data.button.downloading' => 'Downloading...',
                'invoice_data.button.download_from_gus' => 'Download data from GUS',
                'invoice_data.nip_field.error' => '<b>Note:</b> The entered NIP number is incorrect or applies to an entity from outside Poland.',
                'invoice_data.nip_field.data_empty' => '<b>Note:</b> No entity found for the given tax ID.',
                'gus_api.error_processing_request' => '<b>Note:</b> There was an error processing your request.'
            ]
        ];
    }
}