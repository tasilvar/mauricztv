<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\entities;

use bpmj\wpidea\learning\course\{Course_ID, Page_ID};
use bpmj\wpidea\sales\product\model\Product_ID;
use DateTime;

class Course_With_Product
{
    private ?Course_ID $id;
    private Product_ID $product_id;
    private ?Page_ID $page_id;
    private ?string $redirect_page;
    private ?string $redirect_url;
    private ?string $certificate_template_id;
    private ?int $drip_value;
    private ?string $drip_unit;
    private ?DateTime $post_date;
    private ?DateTime $post_date_gmt;
    private ?Course_ID $cloned_from_id;

    private function __construct(
        ?Course_ID $id,
        Product_ID $product_id,
        ?Page_ID $page_id,
        ?string $redirect_page = null,
        ?string $redirect_url = null,
        ?string $certificate_template_id = null,
        ?int $drip_value = null,
        ?string $drip_unit = null,
        ?DateTime $post_date = null,
        ?DateTime $post_date_gmt = null,
        ?Course_ID $cloned_from_id = null
    ) {
        $this->id = $id;
        $this->product_id = $product_id;
        $this->page_id = $page_id;
        $this->redirect_page = $redirect_page;
        $this->redirect_url = $redirect_url;
        $this->certificate_template_id = $certificate_template_id;
        $this->drip_value = $drip_value;
        $this->drip_unit = $drip_unit;
        $this->post_date = $post_date;
        $this->post_date_gmt = $post_date_gmt;
        $this->cloned_from_id = $cloned_from_id;
    }

    public static function create(
        ?Course_ID $id,
        Product_ID $product_id,
        ?Page_ID $page_id,
        ?string $redirect_page = null,
        ?string $redirect_url = null,
        ?string $certificate_template_id = null,
        ?int $drip_value = null,
        ?string $drip_unit = null,
        ?DateTime $post_date = null,
        ?DateTime $post_date_gmt = null,
        ?Course_ID $cloned_from_id = null
    ): self
    {
        return new self(
            $id,
            $product_id,
            $page_id,
            $redirect_page,
            $redirect_url,
            $certificate_template_id,
            $drip_value,
            $drip_unit,
            $post_date,
            $post_date_gmt,
            $cloned_from_id
        );
    }

    public function get_id(): ?Course_ID
    {
        return $this->id;
    }

    public function get_product_id(): Product_ID
    {
        return $this->product_id;
    }

    public function get_page_id(): ?Page_ID
    {
        return $this->page_id;
    }

    public function get_redirect_page(): ?string
    {
        return $this->redirect_page;
    }

    public function get_redirect_url(): ?string
    {
        return $this->redirect_url;
    }

    public function get_certificate_template_id(): ?string
    {
        return $this->certificate_template_id;
    }

    public function get_drip_value(): ?int
    {
        return $this->drip_value;
    }

    public function get_drip_unit(): ?string
    {
        return $this->drip_unit;
    }

    public function get_post_date(): ?DateTime
    {
        return $this->post_date;
    }

    public function get_post_date_gmt(): ?DateTime
    {
        return $this->post_date_gmt;
    }

    public function get_cloned_from_id(): ?Course_ID
    {
        return $this->cloned_from_id;
    }
}
