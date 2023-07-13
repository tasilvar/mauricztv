<?php

namespace bpmj\wpidea\modules\videos\core\services;

use bpmj\wpidea\modules\videos\core\repositories\Interface_Video_Repository;

class Vimeo_Videos_Locator_Service
{
    private const PATTERN_NEW_URL = '/<!-- wp:video {"id":([0-9]*)[^}]*?} -->.<figure class="wp-block-video[^"]*?"><video[^>"]*? src="([^"]*?playback\/([0-9]{9})[^"]*?)"><\/video><\/figure>.*?<!-- \/wp:video -->/is';
    private const PATTERN_OLD_URL = '/<!-- wp:video {"id":([0-9]*)[^}]*?} -->.<figure class="wp-block-video[^"]*?"><video[^>]*?src="(https:\/\/player\.vimeo\.com\/external\/([0-9]{9}?).*?)".*?<!-- \/wp:video -->/is';
    private ?array $vimeo_to_bunny_id_map_cache = null;
    private Interface_Video_Repository $video_repository;

    public function __construct(Interface_Video_Repository $video_repository)
    {
        $this->video_repository = $video_repository;
    }

    public function render(): void
    {
        $this->render_buttons();
        $listed_urls = $this->render_embeds_table();
        $this->render_urls_table($listed_urls);
    }

    public function replace_vimeo_in_all_posts(): void
    {
        foreach ($this->get_posts() as $post) {
            $post->post_content = $this->replace_vimeo_with_bunny($post->post_content);
            wp_update_post($post, false, false);
        }
    }

    protected function render_embeds_table(): array
    {
        $rows = "<h1>Linki w blokach video</h1>
                 <tr>
                     <td></td>
                     <td>URL</td>
                     <td>WP ID</td>
                     <td>URL</td>
                     <td>Vimeo ID</td>
                 </tr>";

        $counter = 0;

        $urls_in_embeds = [];
        foreach ($this->find_videos_in_posts() as $post_id => $post) {
            foreach ($post as $video) {
                ++$counter;
                $post = get_post($post_id);
                $edit_url = get_edit_post_link($post);
                $edit_url = "<a href='{$edit_url}' target='_blank'>{$post->post_title}</a>";
                $rows .= "<tr><td>{$counter}</td><td>{$edit_url}</td>
                          <td>{$video['wp_id']}</td><td><a href='{$video['url']}' target='_blank'>{$video['url']}</a>
                          </td><td>{$video['vimeo_id']}</td></tr>";
                $urls_in_embeds[] = $video['url'];
            }
        }

        echo "<div style='margin-top: 20px'><table>{$rows}</table><style>td{border:1px solid #aaa;}</style></div>";

        return $urls_in_embeds;
    }

    protected function render_urls_table(array $urls_to_skip): void
    {
        $rows = "<h1>Pozostale linki do vimeo</h1>
                 <tr>
                     <td></td>
                     <td>Post</td>
                     <td>Url</td>
                 </tr>";

        $counter = 0;
        foreach ($this->get_posts() as $post) {
            foreach ($this->extract_vimeo_urls($post->post_content) as $matches) {
                foreach ($matches as $match) {
                    if (str_contains($match, 'src="') || in_array($match, $urls_to_skip)) {
                        continue;
                    }
                    ++$counter;
                    $post_edit_url = get_edit_post_link($post);
                    $post_title = $post->post_title;
                    $post_edit_link = "<a href='{$post_edit_url}'>$post_title</a>";
                    $rows .= "<tr><td>{$counter}</td></td><td>{$post_edit_link}</td><td>{$match}</td></tr>";
                }
            }
        }

        echo "<div style='margin-top: 20px'><table>{$rows}</table><style>td{border:1px solid #aaa;}</style></div>";
    }

    protected function render_buttons(): void
    {
        $buttons = "<hr>
                    <a href='/wp-admin/admin.php?page=wpi-vimeo-video&wpi_run_sync=true' 
                       style='background: #0c88b4; padding:5px; border:1px solid cadetblue; color:white'>
                       Synchronizuj
                    </a>
                    <a href='/wp-admin/admin.php?page=wpi-vimeo-video&wpi_run_replace=true' 
                       style='background: #0c88b4; padding:5px; border:1px solid cadetblue; color:white'>
                       Podmien wideo
                    </a>
                   <hr>";

        echo $buttons;
    }

    protected function extract_videos(string $content): ?array
    {
        preg_match_all(self::PATTERN_NEW_URL, $content, $matches_new);
        preg_match_all(self::PATTERN_OLD_URL, $content, $matches_old);

        if (empty($matches_new) && empty($matches_old)) {
            return null;
        }

        $result = [];

        foreach ($matches_new[1] as $key => $id) {
            $result[] = [
                'wp_id' => $id,
                'url' => $matches_new[2][$key],
                'vimeo_id' => $matches_new[3][$key]
            ];
        }

        foreach ($matches_old[1] as $key => $id) {
            $result[] = [
                'wp_id' => $id,
                'url' => $matches_old[2][$key],
                'vimeo_id' => $matches_old[3][$key]
            ];
        }

        return $result;
    }

    protected function replace_vimeo_with_bunny(string $content, $max_recurrency = 100): string
    {
        $new_found = preg_match_all(self::PATTERN_NEW_URL, $content, $matches_new);
        $old_found = preg_match_all(self::PATTERN_OLD_URL, $content, $matches_old);
        if ($new_found + $old_found === 0 || $max_recurrency === 0) {
            return $content;
        }
        $this->vimeo_to_bunny_id_map();
        $replace_callback = function ($matches) {
            $vimeo_id = $matches[3];
            if (array_key_exists($vimeo_id, $this->vimeo_to_bunny_id_map())) {
                $bunny_id = $this->vimeo_to_bunny_id_map()[$vimeo_id];
                return $this->get_bunny_embed_code($bunny_id);
            } else {
                return $matches[0];
            }
        };
        $content = preg_replace_callback(self::PATTERN_NEW_URL, $replace_callback, $content);
        $content = preg_replace_callback(self::PATTERN_OLD_URL, $replace_callback, $content);

        --$max_recurrency;
        return $this->replace_vimeo_with_bunny($content, $max_recurrency);
    }

    protected function vimeo_to_bunny_id_map(): array
    {
        if ($this->vimeo_to_bunny_id_map_cache) {
            return $this->vimeo_to_bunny_id_map_cache;
        }

        $pattern = '/^.*-v-([0-9]{9})-v$/m';

        $results = [];
        foreach ($this->video_repository->find_all() as $video) {
            preg_match($pattern, $video->get_title(), $matches);

            if (isset($matches[1])) {
                $results[$matches[1]] = $video->get_video_id()->get_id();
            }
        }

        $this->vimeo_to_bunny_id_map_cache = $results;
        return $this->vimeo_to_bunny_id_map_cache;
    }

    protected function get_bunny_embed_code(string $uuid): string
    {
        return '<!-- wp:wpi/videos {"videoID":"' . $uuid . '"} /-->';
    }

    protected function extract_vimeo_urls(string $content): array
    {
        $pattern = '/src="(https:\/\/player\.vimeo\.com.*?)"/is';
        preg_match_all($pattern, $content, $matches);
        return $matches;
    }

    private function find_videos_in_posts(): array
    {
        $results = [];
        foreach ($this->get_posts() as $post) {
            $videos = $this->extract_videos($post->post_content);
            if ($videos) {
                $post_id = $post->ID;
                $results[$post_id] = $videos;
            }
        }
        return $results;
    }


    private function get_posts(): array
    {
        return get_posts(
            [
                'numberposts' => -1,
                'post_status' => 'published',
                'post_type' => ['post', 'page', 'tests']
            ]
        );
    }
}