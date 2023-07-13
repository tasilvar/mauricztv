<?php
declare(strict_types=1);

namespace bpmj\wpidea\integrations\invoices\data;

class Wp_Invoice_Remote_Id_Storage implements Interface_Invoice_Remote_Id_Storage
{
    private const META_FIELD_NAME = 'wpi_remote_invoice_id';

    public function store_remote_id(string $invoice_service_slug, int $order_id, int $invoice_id, int $invoice_post_id): void
    {
        update_post_meta($order_id, self::META_FIELD_NAME, array_merge(
            $this->get_remote_invoice_ids($order_id),
            [
                $invoice_service_slug => [
                    'remote_id' => $invoice_id,
                    'post_id' => $invoice_post_id
                ]
            ]
        ));
    }

    public function get_remote_invoice_ids(int $order_id): array
    {
        $value = get_post_meta($order_id, self::META_FIELD_NAME, true);

        return !empty($value) ? $value : [];
    }
}