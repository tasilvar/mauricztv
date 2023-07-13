<?php

namespace bpmj\wpidea\events\handlers;

use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\infrastructure\logs\model\Log_Source;
use bpmj\wpidea\translator\Interface_Translator;
use Psr\Log\LoggerInterface;
use bpmj\wpidea\integrations\invoices\data\Interface_Invoice_Remote_Id_Storage;
use bpmj\wpidea\integrations\invoices\Invoice_Post_Type_To_Service_Slug_Dictionary;

class Invoice_Events_Handler implements Interface_Event_Handler
{
    private LoggerInterface $logger;
    private Interface_Events $events;
    private Interface_Translator $translator;
    private Interface_Invoice_Remote_Id_Storage $invoice_remote_id_storage;

    public function __construct(
        LoggerInterface $logger,
        Interface_Events $events,
        Interface_Translator $translator,
        Interface_Invoice_Remote_Id_Storage $invoice_remote_id_storage
    ) {
        $this->logger = $logger;
        $this->events = $events;
        $this->translator = $translator;
        $this->invoice_remote_id_storage = $invoice_remote_id_storage;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::INVOICE_HAS_BEEN_QUEUED_TO_BE_ISSUED, [$this, 'log_invoice_queued'], 10, 5);
        $this->events->on(Event_Name::INVOICE_NOT_CREATED, [$this, 'log_invoice_created_error'], 10, 3);
        $this->events->on(Event_Name::INVOICE_CREATED_SUCCESSFULLY, [$this, 'log_invoice_created_successfully'], 10, 3);

        $this->events->on(Event_Name::INVOICE_CREATED_SUCCESSFULLY, [$this, 'store_invoice_id'], 10, 5);
    }

    public function log_invoice_queued(
        string $invoice_post_type,
        int $invoice_post_id,
        string $invoice_post_title,
        array $invoice_data,
        array $invoice_source
    ): void {
        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.invoices.queued'),
                $invoice_source['id']
            ),
            [
                'source' => $this->get_source($invoice_post_type),
            ]
        );
    }

    public function log_invoice_created_error(string $invoice_post_type, array $src, string $note): void
    {
        $this->logger->error(
            sprintf(
                $this->translator->translate('logs.invoices.error'),
                $note,
                print_r($src, true)
            ),
            [
                'source' => $this->get_source($invoice_post_type),
            ]
        );
    }

    public function log_invoice_created_successfully(
        string $invoice_post_type,
        array $src,
        string $remote_invoice_number
    ): void {
        $this->logger->info(
            sprintf(
                $this->translator->translate('logs.invoices.success'),
                $remote_invoice_number,
                $src['id']
            ),
            [
                'source' => $this->get_source($invoice_post_type),
            ]
        );
    }

    public function store_invoice_id(
        string $invoice_post_type,
        array $src,
        string $remote_invoice_number,
        int $invoice_post_id,
        ?int $remote_invoice_id
    ): void {
        if (!$remote_invoice_id) {
            return;
        }

        $this->invoice_remote_id_storage->store_remote_id(
            Invoice_Post_Type_To_Service_Slug_Dictionary::DIC[$invoice_post_type] ?? $invoice_post_type,
            $src['id'],
            $remote_invoice_id,
            $invoice_post_id
        );
    }

    private function get_source(string $invoice_post_type): string
    {
        switch ($invoice_post_type) {
            case 'bpmj_wp_fakturownia':
                return Log_Source::INVOICE_FAKTUROWNIA;
            case 'bpmj_wp_ifirma':
                return Log_Source::INVOICE_IFIRMA;
            case 'bpmj_wp_wfirma':
                return Log_Source::INVOICE_WFIRMA;
            case 'bpmj_wp_infakt':
                return Log_Source::INVOICE_INFAKT;
            case 'bpmj_wp_taxe':
                return Log_Source::INVOICE_TAXE;
            default:
                return $invoice_post_type;
        }
    }
}