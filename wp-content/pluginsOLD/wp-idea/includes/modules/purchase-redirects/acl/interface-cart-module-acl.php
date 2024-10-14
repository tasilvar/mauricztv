<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\acl;

interface Interface_Cart_Module_ACL
{
   public function is_success_page(): bool;

   public function get_payment_id_from_session(): int;
}