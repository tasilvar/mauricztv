<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\integrations\invoices\email;

interface Interface_Sendable_By_Email
{
    public function send_by_email(int $invoice_id): void;
}