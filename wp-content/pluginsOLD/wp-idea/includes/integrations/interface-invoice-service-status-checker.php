<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\integrations;

interface Interface_Invoice_Service_Status_Checker
{
    public function is_integration_enabled(string $service_name): bool;
}