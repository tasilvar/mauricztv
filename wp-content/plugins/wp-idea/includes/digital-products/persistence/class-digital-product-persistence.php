<?php
declare(strict_types=1);

namespace bpmj\wpidea\digital_products\persistence;

use bpmj\wpidea\resources\Resource_Type;
use WP_Query;

class Digital_Product_Persistence implements Interface_Digital_Product_Persistence
{
    private const DB_POST_TYPE_SLUG = 'download';

    private const RESOURCE_TYPE_META_NAME = 'wpi_resource_type';
    private const INCLUDED_FILES_META_NAME = 'edd_download_files';

    public function find_by_id(int $id): ?array
    {
        $posts = get_posts(array_merge($this->get_default_query_args(), [
            'posts_per_page' => 1,
            'p' => $id
        ]));

        if(empty($posts)) {
            return null;
        }

        $post = $posts[0];

        return [
            'id' => $id,
            'name' => $post->post_title
        ];
    }

    public function find_all(): array
    {
        $products = [];
        $posts = get_posts(array_merge($this->get_default_query_args(), [
            'posts_per_page' => -1,
        ]));

        foreach ($posts as $post) {
            $products[] = [
                'id' => $post->ID,
                'name' => $post->post_title
            ];
        }

        return $products;
    }

    public function find_files_by_product_id(int $product_id): array
    {
        return get_post_meta($product_id, self::INCLUDED_FILES_META_NAME, true) ?? [];
    }

    public function update_files_by_product_id(int $product_id, array $files): void
    {
        update_post_meta($product_id, self::INCLUDED_FILES_META_NAME, $files);
    }

    public function save_or_update_product(?int $id, string $name): int
    {
        $args = $this->get_default_update_query_args($id, $name);

        return $id ? wp_update_post($args) : wp_insert_post($args);
    }

    public function count_all(): int
    {
        return (new WP_Query(array_merge($this->get_default_query_args(), [
            'number' => 1,
            'fields' => 'ids'
        ])))->found_posts;
    }

    private function get_default_update_query_args(?int $id, string $name): array
    {
        return array_filter([
            'ID' => $id,
            'post_type' => 'download',
            'post_title' => $name,
            'post_status' => 'publish',
            'comment_status' => 'closed',
            'meta_input' => [
                self::RESOURCE_TYPE_META_NAME => Resource_Type::DIGITAL_PRODUCT,
            ],
        ]);
    }

    private function get_default_query_args(): array
    {
        return [
            'post_type' => self::DB_POST_TYPE_SLUG,
            'meta_query' => [
                [
                    'key' => self::RESOURCE_TYPE_META_NAME,
                    'value' => Resource_Type::DIGITAL_PRODUCT
                ],
            ]
        ];
    }
}