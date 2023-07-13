<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\courses\core\dto;

class Course_DTO
{
    public ?int $id = null;

    public ?int $product_id = null;

    public ?int $page_id = null;

    public ?string $redirect_page = null;

    public ?string $redirect_url = null;

    public ?string $certificate_template_id = null;

    public ?int $drip_value = null;

    public ?string $drip_unit = null;

    public ?string $post_date = null;

    public ?string $post_date_gmt = null;

    public ?int $cloned_from_id = null;
}