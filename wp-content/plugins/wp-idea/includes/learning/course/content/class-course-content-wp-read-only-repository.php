<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\learning\course\content;

class Course_Content_Wp_Read_Only_Repository implements Interface_Readable_Course_Content_Repository
{
    public function find_by_query(string $query): Course_Content_Collection
    {
        $result = new \WP_Query([
            's' => $query,
            'post_type' => 'page',
            'meta_query' => [
                [
                    'key' => 'mode',
                    'value' => [
                        Course_Content_Type::TYPE_PANEL,
                        Course_Content_Type::TYPE_MODULE,
                        Course_Content_Type::TYPE_LESSON,
                        Course_Content_Type::TYPE_QUIZ
                    ],
                    'compare' => 'IN'
                ]
            ],
            'posts_per_page' => -1
        ]);

        $course_content_collection = new Course_Content_Collection();

        foreach($result->posts as $post) {
            $course_content_collection->append(
                new Course_Content(
                    new Course_Content_ID($post->ID),
                    new Course_Content_Type($post->mode),
                    $post->post_title,
                    $post->post_content
                )
            );
        }

        return $course_content_collection;
    }

    public function find_by_id(Course_Content_ID $course_content_id): ?Course_Content
    {
        $result = new \WP_Query([
            'p' => $course_content_id->to_int(),
            'post_type' => 'page',
            'meta_query' => [
                [
                    'key' => 'mode',
                    'value' => [
                        Course_Content_Type::TYPE_PANEL,
                        Course_Content_Type::TYPE_MODULE,
                        Course_Content_Type::TYPE_LESSON,
                        Course_Content_Type::TYPE_QUIZ
                    ],
                ]
            ],
        ]);
       
        if(!isset($result->posts[0])){
            return null;
        }

        $post = $result->posts[0];

        $course_content_repository = new Course_Content(
            new Course_Content_ID($post->ID),
            new Course_Content_Type($post->mode),
            $post->post_title,
            $post->post_content
        );

        return $course_content_repository;
    }    
}
