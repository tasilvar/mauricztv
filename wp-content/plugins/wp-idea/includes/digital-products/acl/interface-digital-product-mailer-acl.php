<?php
declare(strict_types=1);

namespace bpmj\wpidea\digital_products\acl;

use WP_Post;

interface Interface_Digital_Product_Mailer_ACL
{
    public function save_mailers(WP_Post $post, array $mailers): void;
}