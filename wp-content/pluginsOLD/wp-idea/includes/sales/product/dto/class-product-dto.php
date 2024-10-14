<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\dto;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\sales\product\model\Gtu;
use bpmj\wpidea\sales\product\model\Product_Price;
use DateTime;

class Product_DTO
{
    public ?int $id = null;

    public string $name;

    public string $description;

    public ?string $short_description = null;

    public float $price;

    public ?int $linked_resource_id = null;

    public ?string $slug = null;

    public ?string $banner = null;

    public ?string $featured_image = null;

    public bool $sales_disabled = false;

    public array $categories = [];

    public bool $hide_from_list = false;

    public bool $hide_purchase_button = false;

    public ?string $flat_rate_tax_symbol = null;

    public ?string $vat_rate = null;

    public string $gtu = Gtu::NO_GTU;

    public ?float $sale_price = null;

    public ?string $sale_price_date_from = null;

    public ?string $sale_price_date_to = null;

    public ?int $purchase_limit = null;

    public ?int $purchase_limit_items_left = null;

    public bool $promote_curse = false;

    public bool $recurring_payments_enabled = false;

    public ?string $recurring_payments_interval = null;

    public string $tags = '';

    public array $mailchimp = [];

    public ?string $sell_discount_code = null;

    public ?string $discount_code_period_validity = null;

    public array $mailerlite = [];

    public array $freshmail = [];

    public string $ipresso_tags = '';

    public string $ipresso_tags_unsubscribe = '';

    public array $activecampaign = [];

    public array $activecampaign_unsubscribe = [];

    public string $activecampaign_tags = '';

    public string $activecampaign_tags_unsubscribe = '';

    public array $getresponse = [];

    public array $getresponse_unsubscribe = [];

    public array $getresponse_tags = [];

    public string $salesmanago_tags = '';

    public array $interspire = [];

    public array $convertkit = [];

    public array $convertkit_tags = [];

    public array $convertkit_tags_unsubscribe = [];

    public bool $variable_pricing_enabled = false;

    public ?array $variable_prices = null;

    public ?float $tmp_sale_price = null;

    public ?float $effective_sale_price = null;

    public ?string $variable_sale_price_date_from = null;

    public ?string $variable_sale_price_date_to = null;

    public ?string $access_time_and_unit = null;

    public bool $access_start_enabled = false;

    public ?string $access_start = null;

    public ?string $custom_purchase_link = null;

    public ?int $thumbnail_id = null;

    public bool $disable_certificates = false;

    public bool $enable_certificate_numbering = false;

    public bool $disable_email_subscription = false;

    public ?string $certificate_numbering_pattern = null;

    public ?string $logo = null;

    public ?string $gtu_variable_prices = null;

    public ?string $flat_rate_tax_symbol_variable_prices = null;

    public ?string $navigation_next_lesson_label = null;

    public ?string $navigation_previous_lesson_label = null;

    public ?string $progress_tracking = null;

    public ?string $inaccessible_lesson_display = null;

    public ?string $progress_forced = null;

    public bool $is_bundle = false;

    public array $bundled_products = [];
}