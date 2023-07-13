<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

use bpmj\wpidea\data_types\ID;
use bpmj\wpidea\sales\product\model\collection\Product_Variant_Collection;
use DateTime;

class Product
{
    private ?Product_ID $id;

    private Product_Name $name;

    private Product_Price $price;

    private Product_Description $description;

    private ?Product_Short_Description $short_description;

    private ?ID $linked_resource_id;

    private ?string $slug;

    private bool $is_bundle;

    private ?string $banner;

    private ?string $featured_image;

    private bool $sales_disabled;

    private array $categories;

    private bool $hide_from_list;

    private bool $hide_purchase_button;

    private ?Product_Flat_Rate_Tax_Symbol $flat_rate_tax_symbol;

    private Gtu $gtu;

    private ?string $vat_rate;

    private ?Product_Price $sale_price;

    private ?DateTime $sale_price_date_from;

    private ?DateTime $sale_price_date_to;

    private ?int $purchase_limit;
    private ?int $purchase_limit_items_left;

    private bool $promote_course;

    private bool $recurring_payments_enabled;

    private ?string $recurring_payments_interval;

    private ?Product_Mailers_Settings $mailers_settings;
    private ?Product_Tags $tags;
    private ?Product_Discount_Code_Settings $discount_code_settings;
    private ?Product_Variant_Collection $product_variants;
    private bool $variable_pricing_enabled;

    private ?Product_Price $tmp_sale_price;
    private ?Product_Price $effective_sale_price;
    private ?DateTime $variable_sale_price_date_from;
    private ?DateTime $variable_sale_price_date_to;
    private ?int $access_time;
    private ?string $access_time_unit;
    private ?DateTime $access_start;
    private bool $access_start_enabled;
    private ?string $custom_purchase_link;
    private ?int $thumbnail_id;
    private bool $disable_certificates;
    private bool $enable_certificate_numbering;
    private bool $disable_email_subscription;
    private ?string $certificate_numbering_pattern;
    private ?string $logo;
    private ?array $variable_prices;
    private ?string $gtu_variable_prices;
    private ?string $flat_rate_tax_symbol_variable_prices;
    private ?string $navigation_next_lesson_label;
    private ?string $navigation_previous_lesson_label;
    private ?string $progress_tracking;
    private ?string $inaccessible_lesson_display;
    private ?string $progress_forced;
    private array $bundled_products;

    private function __construct(
        ?Product_ID $id,
        Product_Name $name,
        Product_Description $description,
        ?Product_Short_Description $short_description,
        Product_Price $price,
        ?ID $linked_resource_id,
        ?string $slug = null,
        bool $is_bundle,
        Gtu $gtu,
        ?string $banner = null,
        ?string $featured_image = null,
        bool $sales_disabled = false,
        array $categories = [],
        bool $hide_from_list = false,
        bool $hide_purchase_button = false,
        ?Product_Flat_Rate_Tax_Symbol $flat_rate_tax_symbol = null,
        ?string $vat_rate = null,
        ?Product_Price $sale_price = null,
        ?DateTime $sale_price_date_from = null,
        ?DateTime $sale_price_date_to = null,
        ?int $purchase_limit = null,
        ?int $purchase_limit_items_left = null,
        bool $promote_course = false,
        bool $recurring_payments_enabled = false,
        ?string $recurring_payments_interval = null,
        ?Product_Mailers_Settings $mailers_settings = null,
        ?Product_Tags $tags = null,
        ?Product_Discount_Code_Settings $discount_code_settings = null,
        ?Product_Variant_Collection $product_variants = null,
        ?array $variable_prices = null,
        bool $variable_pricing_enabled = false,
        ?Product_Price $tmp_sale_price = null,
        ?Product_Price $effective_sale_price = null,
        ?DateTime $variable_sale_price_date_from = null,
        ?DateTime $variable_sale_price_date_to = null,
        ?int $access_time = null,
        ?string $access_time_unit = null,
        bool $access_start_enabled = false,
        ?DateTime $access_start = null,
        ?string $custom_purchase_link = null,
        ?int $thumbnail_id = null,
        bool $disable_certificates = false,
        bool $enable_certificate_numbering = false,
        bool $disable_email_subscription = false,
        ?string $certificate_numbering_pattern = null,
        ?string $logo = null,
        ?string $gtu_variable_prices = null,
        ?string $flat_rate_tax_symbol_variable_prices = null,
        ?string $navigation_next_lesson_label = null,
        ?string $navigation_previous_lesson_label = null,
        ?string $progress_tracking = null,
        ?string $inaccessible_lesson_display = null,
        ?string $progress_forced = null,
        array $bundled_products = []
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->short_description = $short_description;
        $this->price = $price;
        $this->linked_resource_id = $linked_resource_id;
        $this->slug = $slug;
        $this->is_bundle = $is_bundle;
        $this->gtu = $gtu;
        $this->banner = $banner;
        $this->featured_image = $featured_image;
        $this->sales_disabled = $sales_disabled;
        $this->categories = $categories;
        $this->hide_from_list = $hide_from_list;
        $this->hide_purchase_button = $hide_purchase_button;
        $this->flat_rate_tax_symbol = $flat_rate_tax_symbol;
        $this->vat_rate = $vat_rate;
        $this->sale_price = $sale_price;
        $this->sale_price_date_from = $sale_price_date_from;
        $this->sale_price_date_to = $sale_price_date_to;
        $this->purchase_limit = $purchase_limit;
        $this->purchase_limit_items_left = $purchase_limit_items_left;
        $this->promote_course = $promote_course;
        $this->recurring_payments_enabled = $recurring_payments_enabled;
        $this->recurring_payments_interval = $recurring_payments_interval;
        $this->mailers_settings = $mailers_settings;
        $this->tags = $tags;
        $this->discount_code_settings = $discount_code_settings;
        $this->product_variants = $product_variants;
        $this->variable_prices = $variable_prices;
        $this->variable_pricing_enabled = $variable_pricing_enabled;
        $this->tmp_sale_price = $tmp_sale_price;
        $this->effective_sale_price = $effective_sale_price;
        $this->variable_sale_price_date_from = $variable_sale_price_date_from;
        $this->variable_sale_price_date_to = $variable_sale_price_date_to;
        $this->access_time = $access_time;
        $this->access_time_unit = $access_time_unit;
        $this->access_start_enabled = $access_start_enabled;
        $this->access_start = $access_start;
        $this->custom_purchase_link = $custom_purchase_link;
        $this->thumbnail_id = $thumbnail_id;
        $this->disable_certificates = $disable_certificates;
        $this->enable_certificate_numbering = $enable_certificate_numbering;
        $this->disable_email_subscription = $disable_email_subscription;
        $this->certificate_numbering_pattern = $certificate_numbering_pattern;
        $this->logo = $logo;
        $this->gtu_variable_prices = $gtu_variable_prices;
        $this->flat_rate_tax_symbol_variable_prices = $flat_rate_tax_symbol_variable_prices;
        $this->navigation_next_lesson_label = $navigation_next_lesson_label;
        $this->navigation_previous_lesson_label = $navigation_previous_lesson_label;
        $this->progress_tracking = $progress_tracking;
        $this->inaccessible_lesson_display = $inaccessible_lesson_display;
        $this->progress_forced = $progress_forced;
        $this->bundled_products = $bundled_products;
    }

    public static function create(
        ?Product_ID $id,
        Product_Name $name,
        Product_Description $description,
        ?Product_Short_Description $short_description,
        Product_Price $price,
        ?ID $linked_resource_id,
        ?string $slug,
        bool $is_bundle,
        Gtu $gtu,
        ?string $banner = null,
        ?string $featured_image = null,
        bool $sales_disabled = false,
        array $categories = [],
        bool $hide_from_list = false,
        bool $hide_purchase_button = false,
        ?Product_Flat_Rate_Tax_Symbol $flat_rate_tax_symbol = null,
        ?string $vat_rate = null,
        ?Product_Price $sale_price = null,
        ?DateTime $sale_price_date_from = null,
        ?DateTime $sale_price_date_to = null,
        ?int $purchase_limit = null,
        ?int $purchase_limit_items_left = null,
        bool $promote_course = false,
        bool $recurring_payments_enabled = false,
        ?string $recurring_payments_interval = null,
        ?Product_Mailers_Settings $mailers_settings = null,
        ?Product_Tags $tags = null,
        ?Product_Discount_Code_Settings $discount_code_settings = null,
        ?Product_Variant_Collection $product_variants = null,
        ?array $variable_prices = null,
        bool $variable_pricing_enabled = false,
        ?Product_Price $tmp_sale_price = null,
        ?Product_Price $effective_sale_price = null,
        ?DateTime $variable_sale_price_date_from = null,
        ?DateTime $variable_sale_price_date_to = null,
        ?int $access_time = null,
        ?string $access_time_unit = null,
        bool $access_start_enabled = false,
        ?DateTime $access_start = null,
        ?string $custom_purchase_link = null,
        ?int $thumbnail_id = null,
        bool $disable_certificates = false,
        bool $enable_certificate_numbering = false,
        bool $disable_email_subscription = false,
        ?string $certificate_numbering_pattern = null,
        ?string $logo = null,
        ?string $gtu_variable_prices = null,
        ?string $flat_rate_tax_symbol_variable_prices = null,
        ?string $navigation_next_lesson_label = null,
        ?string $navigation_previous_lesson_label = null,
        ?string $progress_tracking = null,
        ?string $inaccessible_lesson_display = null,
        ?string $progress_forced = null,
        array $bundled_products = []
    ): self {
        $mailers_settings = $mailers_settings ?? new Product_Mailers_Settings();
        $tags = $tags ?? Product_Tags::create_from_array([]);

        return new self(
            $id,
            $name,
            $description,
            $short_description,
            $price,
            $linked_resource_id,
            $slug,
            $is_bundle,
            $gtu,
            $banner,
            $featured_image,
            $sales_disabled,
            $categories,
            $hide_from_list,
            $hide_purchase_button,
            $flat_rate_tax_symbol,
            $vat_rate,
            $sale_price,
            $sale_price_date_from,
            $sale_price_date_to,
            $purchase_limit,
            $purchase_limit_items_left,
            $promote_course,
            $recurring_payments_enabled,
            $recurring_payments_interval,
            $mailers_settings,
            $tags,
            $discount_code_settings,
            $product_variants,
            $variable_prices,
            $variable_pricing_enabled,
            $tmp_sale_price,
            $effective_sale_price,
            $variable_sale_price_date_from,
            $variable_sale_price_date_to,
            $access_time,
            $access_time_unit,
            $access_start_enabled,
            $access_start,
            $custom_purchase_link,
            $thumbnail_id,
            $disable_certificates,
            $enable_certificate_numbering,
            $disable_email_subscription,
            $certificate_numbering_pattern,
            $logo,
            $gtu_variable_prices,
            $flat_rate_tax_symbol_variable_prices,
            $navigation_next_lesson_label,
            $navigation_previous_lesson_label,
            $progress_tracking,
            $inaccessible_lesson_display,
            $progress_forced,
            $bundled_products
        );
    }

    public function get_purchase_limit(): ?int
    {
        return $this->purchase_limit;
    }

    public function get_purchase_limit_items_left(): ?int
    {
        return $this->purchase_limit_items_left;
    }

    public function get_sale_price_date_from(): ?DateTime
    {
        return $this->sale_price_date_from;
    }

    public function get_sale_price_date_to(): ?DateTime
    {
        return $this->sale_price_date_to;
    }

    public function get_sale_price(): ?Product_Price
    {
        return $this->sale_price;
    }

    public function get_effective_sale_price(): ?Product_Price
    {
        return $this->effective_sale_price;
    }

    public function get_id(): ?Product_ID
    {
        return $this->id;
    }

    public function set_id(Product_ID $id): void
    {
        $this->id = $id;
    }

    public function get_name(): Product_Name
    {
        return $this->name;
    }

    public function get_short_description(): ?Product_Short_Description
    {
        return $this->short_description;
    }

    public function get_description(): Product_Description
    {
        return $this->description;
    }

    public function get_price(): Product_Price
    {
        return $this->price;
    }

    public function get_linked_resource_id(): ?ID
    {
        return $this->linked_resource_id;
    }

    public function set_linked_resource_id(ID $resource_id): void
    {
        $this->linked_resource_id = $resource_id;
    }

    public function is_bundle(): bool
    {
        return $this->is_bundle;
    }

    public function get_banner(): ?string
    {
        return $this->banner;
    }

    public function get_featured_image(): ?string
    {
        return $this->featured_image;
    }

    public function change_sales_disabled(bool $sales_disabled): void
    {
        $this->sales_disabled = $sales_disabled;
    }

    public function sales_disabled(): bool
    {
        return $this->sales_disabled;
    }

    public function get_categories(): array
    {
        return $this->categories;
    }

    public function hide_from_list(): bool
    {
        return $this->hide_from_list;
    }

    public function hide_purchase_button(): bool
    {
        return $this->hide_purchase_button;
    }

    public function get_flat_rate_tax_symbol(): ?Product_Flat_Rate_Tax_Symbol
    {
        return $this->flat_rate_tax_symbol;
    }

    public function get_gtu(): Gtu
    {
        return $this->gtu;
    }

    public function get_vat_rate(): ?string
    {
        return $this->vat_rate;
    }

    public function get_promote_course(): bool
    {
        return $this->promote_course;
    }

    public function get_recurring_payments_enabled(): bool
    {
        return $this->recurring_payments_enabled;
    }

    public function get_recurring_payments_interval(): ?string
    {
        return $this->recurring_payments_interval;
    }

    public function get_mailers_settings(): ?Product_Mailers_Settings
    {
        return $this->mailers_settings;
    }

    public function get_tags(): Product_Tags
    {
        return $this->tags;
    }

    public function get_discount_code_settings(): ?Product_Discount_Code_Settings
    {
        return $this->discount_code_settings;
    }

    public function get_slug(): ?string
    {
        return $this->slug;
    }

    public function get_product_variants(): ?Product_Variant_Collection
    {
        return $this->product_variants;
    }

    public function get_variable_prices(): ?array
    {
        return $this->variable_prices;
    }

    public function get_gtu_variable_prices(): ?string
    {
        return $this->gtu_variable_prices;
    }

    public function get_flat_rate_tax_symbol_variable_prices(): ?string
    {
        return $this->flat_rate_tax_symbol_variable_prices;
    }

    public function get_variable_pricing_enabled(): bool
    {
        return $this->variable_pricing_enabled;
    }

    public function get_tmp_sale_price(): ?Product_Price
    {
        return $this->tmp_sale_price;
    }

    public function get_variable_sale_price_date_from(): ?DateTime
    {
        return $this->variable_sale_price_date_from;
    }

    public function get_variable_sale_price_date_to(): ?DateTime
    {
        return $this->variable_sale_price_date_to;
    }

    public function get_access_time(): ?int
    {
        return $this->access_time;
    }

    public function get_access_time_unit(): ?string
    {
        return $this->access_time_unit;
    }

    public function get_access_start_enabled(): bool
    {
        return $this->access_start_enabled;
    }

    public function get_access_start(): ?DateTime
    {
        return $this->access_start;
    }

    public function get_custom_purchase_link(): ?string
    {
        return $this->custom_purchase_link;
    }

    public function get_thumbnail_id(): ?int
    {
        return $this->thumbnail_id;
    }

    public function get_disable_certificates(): bool
    {
        return $this->disable_certificates;
    }

    public function get_enable_certificate_numbering(): bool
    {
        return $this->enable_certificate_numbering;
    }

    public function get_disable_email_subscription(): bool
    {
        return $this->disable_email_subscription;
    }

    public function get_certificate_numbering_pattern(): ?string
    {
        return $this->certificate_numbering_pattern;
    }

    public function get_logo(): ?string
    {
        return $this->logo;
    }

    public function get_navigation_next_lesson_label(): ?string
    {
        return $this->navigation_next_lesson_label;
    }

    public function get_navigation_previous_lesson_label(): ?string
    {
        return $this->navigation_previous_lesson_label;
    }

    public function get_progress_tracking(): ?string
    {
        return $this->progress_tracking;
    }

    public function get_inaccessible_lesson_display(): ?string
    {
        return $this->inaccessible_lesson_display;
    }

    public function get_progress_forced(): ?string
    {
        return $this->progress_forced;
    }

    public function get_bundled_products(): array
    {
        return $this->bundled_products;
    }

    public function has_active_promotion(): bool
    {
        if (is_null($this->get_effective_sale_price())) {
            return false;
        }

        $sale_price_date_from = $this->get_sale_price_date_from() ?? new \DateTime('1970-01-01');
        $sale_price_date_to = $this->get_sale_price_date_to() ?? new \DateTime('3000-01-01');
        $current_date = new \DateTime('now');
        return $sale_price_date_from <= $current_date && $sale_price_date_to >= $current_date;
    }

    public function get_variants_with_promotion(): Product_Variant_Collection
    {
        $product_variants_with_promotion = new Product_Variant_Collection();
        foreach ($this->get_product_variants() ?? [] as $product_variant) {
            if (empty($product_variant->get_sale_price())) {
                continue;
            }

            $product_variants_with_promotion->add($product_variant);
        }

        return $product_variants_with_promotion;
    }

    public function has_access_time(): bool
    {
        if (!$this->get_variable_pricing_enabled()) {
            return !empty($this->get_access_time());
        }

        foreach ($this->get_product_variants() ?? [] as $product_variant) {
            if (!empty($product_variant->get_access_time())) {
               return true;
            }
        }

        return false;
    }
}
