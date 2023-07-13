<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\modules\increasing_sales\core\dto;

class Offer_Data_DTO
{
    public int $id_offer;

    public string $product_id;

    public string $offer_type;

    public string $offered_product_id;

    public string $title;

    public string $description;

    public string $image_url;

    public ?float $discount;
}