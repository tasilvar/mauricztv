<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\integrations\invoices\email;

use bpmj\wpidea\integrations\invoices\data\Interface_Invoice_Remote_Id_Storage;
use BPMJ_Base_Invoice;
use bpmj\wpidea\integrations\Interface_Invoice_Service_Status_Checker;
use bpmj\wpidea\integrations\invoices\Invoice_Service_Name_To_Class_Dictionary;

class Invoice_Email_Sending_Service
{
    private Interface_Invoice_Remote_Id_Storage $invoice_remote_id_storage;
    private Interface_Invoice_Service_Status_Checker $invoice_service_status_checker;

    public function __construct(
        Interface_Invoice_Remote_Id_Storage $invoice_remote_id_storage,
        Interface_Invoice_Service_Status_Checker $invoice_service_status_checker
    )
    {
        $this->invoice_remote_id_storage = $invoice_remote_id_storage;
        $this->invoice_service_status_checker = $invoice_service_status_checker;
    }

    public function send_invoices_for_order_by_email(int $order_id): void
    {
        foreach ($this->get_remote_ids($order_id) as $service_slug => $invoice_data) {
            $invoice_id = $invoice_data['remote_id'] ?? null;
            $invoice_post_id = $invoice_data['post_id'] ?? null;

            if(!$invoice_id || !$invoice_post_id) {
                continue;
            }

            if(!$this->service_enabled($service_slug)) {
                continue;
            }

            $this->try_to_send_invoice_by_email($service_slug, $invoice_id, $invoice_post_id);
        }
    }

    private function get_remote_ids(int $order_id): array
    {
        return $this->invoice_remote_id_storage->get_remote_invoice_ids($order_id);
    }

    private function service_enabled(string $service_slug): bool
    {
        return $this->invoice_service_status_checker->is_integration_enabled($service_slug);
    }

    private function try_to_send_invoice_by_email(string $service_slug, int $invoice_id, int $invoice_post_id): void
    {
        $invoice_object = $this->get_invoice_object_by_service_slug($service_slug);

        if(!$invoice_object) {
            return;
        }

        if(!$invoice_object instanceof Interface_Sendable_By_Email) {
            return;
        }

        $invoice_object->set_from_invoice_post($invoice_post_id);
        $invoice_object->send_by_email($invoice_id);
    }

    private function get_invoice_object_by_service_slug(string $service_slug): ?BPMJ_Base_Invoice
    {
        try {
            $invoice_class = Invoice_Service_Name_To_Class_Dictionary::DIC[$service_slug];

            return new $invoice_class();
        } catch (\Exception $e) {
            return null;
        }
    }
}