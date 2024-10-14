<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\acl;

class Cart_Module_ACL implements Interface_Cart_Module_ACL
{
    private const PURCHASE_KEY_SESSION = 'purchase_key';
    private const SUCCESS_PAGE = 'success_page';

    public function is_success_page(): bool
    {
        return is_page(edd_get_option(self::SUCCESS_PAGE));
    }

    public function get_payment_id_from_session(): int
    {
        $session = edd_get_purchase_session();

        return (int) edd_get_purchase_id_by_key($session[self::PURCHASE_KEY_SESSION]);
    }

}