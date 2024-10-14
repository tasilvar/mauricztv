<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\integrations\invoices\data;

interface Interface_Invoice_Remote_Id_Storage
{
    public function store_remote_id(string $invoice_service_slug, int $order_id, int $invoice_id, int $invoice_post_id): void;

    public function get_remote_invoice_ids(int $order_id): array;
}