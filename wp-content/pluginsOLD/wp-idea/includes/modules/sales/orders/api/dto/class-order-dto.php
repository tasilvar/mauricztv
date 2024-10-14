<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\sales\orders\api\dto;

class Order_DTO
{
    public ?int $id = null;
    public ?int $user_id = null;
    public Client_DTO $client;
    public string $date;
    public string $status;
    public float $total;
    public string $currency;
    public Cart_Content_DTO $cart_content;
}