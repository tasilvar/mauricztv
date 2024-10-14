<?php
declare(strict_types=1);

namespace bpmj\wpidea\physical_product\persistence;

use bpmj\wpidea\physical_product\dto\Physical_Product_DTO;
use bpmj\wpidea\physical_product\dto\Physical_Product_DTO_Collection;
use bpmj\wpidea\resources\Resource_Type;
use WP_Query;

class Physical_Product_Persistence implements Interface_Physical_Product_Persistence
{
    private const DB_POST_TYPE_SLUG = 'download';

    private const RESOURCE_TYPE_META_NAME = 'wpi_resource_type';

    public function find_by_id(int $id): ?Physical_Product_DTO
    {
        $posts = get_posts(array_merge($this->get_default_query_args(), [
            'posts_per_page' => 1,
            'p' => $id
        ]));

        if(empty($posts)) {
            return null;
        }

        $post = $posts[0];

        $dto = new Physical_Product_DTO();
        $dto->id = $id;
        $dto->name = $post->post_title;

        return $dto;
    }

    public function find_all(): Physical_Product_DTO_Collection
    {
        $collection = Physical_Product_DTO_Collection::create();
        $posts = get_posts(array_merge($this->get_default_query_args(), [
            'posts_per_page' => -1,
        ]));

        foreach ($posts as $post) {
            $dto = new Physical_Product_DTO();
            $dto->id = $post->ID;
            $dto->name = $post->post_title;
            $collection->add($dto);
        }

        return $collection;
    }

    public function save_or_update_physical_product(?int $id, string $name): int
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
                self::RESOURCE_TYPE_META_NAME => Resource_Type::PHYSICAL_PRODUCT,
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
                    'value' => Resource_Type::PHYSICAL_PRODUCT
                ],
            ]
        ];
    }
}
