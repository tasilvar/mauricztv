<?php
declare(strict_types=1);

namespace bpmj\wpidea\digital_products\acl;

use WP_Post;

class Digital_Product_Mailer_ACL implements Interface_Digital_Product_Mailer_ACL
{

    public function save_mailers(WP_Post $post, array $mailers): void
    {
        global $post;

        if(!$mailers){
            return;
        }

        $meta_input = [];

        foreach ($mailers as $mailer_slug => $mailer) {
            $meta_input[$mailer_slug] = $mailer;
        }

        $args = [
            'ID' => $post->ID,
            'meta_input' => $meta_input,
        ];

        wp_update_post($args);
    }

}