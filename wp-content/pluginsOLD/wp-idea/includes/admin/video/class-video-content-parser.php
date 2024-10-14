<?php

namespace bpmj\wpidea\admin\video;

class Video_Content_Parser {
    /**
     * Content filter should have high priority to run early 
     * (before Wordpress replaces video links with video embeds)
     */
    private const CONTENT_FILTER_PRIORITY = 1;

    public function __construct() {
        add_filter( 'the_content', [$this, 'filter_content'], self::CONTENT_FILTER_PRIORITY );
    }

    public function filter_content( $content )
    {
        $content = $this->replace_players_with_new_vimeo_url($content);

        $vimeo_direct_link_pattern      = $this->_get_vimeo_direct_link_pattern();
        $gutenberg_video_embed_pattern  = $this->_get_gutenberg_vimeo_embed_pattern('wp-block-video');        
        $gutenberg_video_embed_with_caption_pattern  = $this->_get_gutenberg_vimeo_embed_with_caption_pattern('wp-block-video');

        $content = preg_replace([$gutenberg_video_embed_with_caption_pattern], '<figure class="video-embed-figure"><p>https://player.vimeo.com/video/$7</p>$11</figure>', $content);

        $content = preg_replace([$vimeo_direct_link_pattern, $gutenberg_video_embed_pattern], 'https://player.vimeo.com/video/$7', $content);

        /**
         * In Media/Text block links are not replaced with embeds automatically
         */
        $gutenberg_media_text_embed_pattern  = $this->_get_gutenberg_vimeo_embed_pattern('wp-block-media-text__media');
        $content = preg_replace([$gutenberg_media_text_embed_pattern], '<div class="embed-responsive embed-responsive-16by9"><iframe src="https://player.vimeo.com/video/$7" class="embed-responsive-item"></iframe></div>', $content);

        return $content;
    }

    private function replace_players_with_new_vimeo_url($content): string
    {
        $regex = '/<figure\s+(?:[^>]*?\s+)?class="wp-block-video.*"><video\s+(?:[^>]*?\s+)?src="(https\:\/\/player.vimeo.com\/progressive_redirect\/playback\/(.{9})\/.*)<\s*\/\s*video><\s*\/\s*figure>/';

        $embed_code = '
            <div style="padding:56.25% 0 0 0;position:relative;">
                <iframe src="https://player.vimeo.com/video/${2}" 
                frameborder="0" allow="autoplay; fullscreen; picture-in-picture" allowfullscreen style="position:absolute;top:0;left:0;width:100%;height:100%;" 
                title="">
                </iframe>
            </div>';

        return preg_replace(
            $regex,
            $embed_code,
            $content
        );
    }

    /**
     * Pattern that matches links like:
     * 
     * <a href="https://player.vimeo.com/external/{vimeo_id}?s=...">{file_name}</a>
     * 
     * {vimeo_id} == 7th group ($7 in the replacement)
     */
    private function _get_vimeo_direct_link_pattern(): string
    {
        return '/<a\s+(?:[^>]*?\s+)?href=(["\'])((http[s]?|ftp):\/)?\/?(player.vimeo.com)((\/\w+)*\/)([\w\-\]+[^#?\s]+)(.*)?(#[\w\-]+)?<\s*\/\s*a>/';
    }

    /**
     * Pattern that matches strings like:
     *
     * <figure class="wp-block-video alignleft"><video controls="" src="https://player.vimeo.com/external/{vimeo_id}?s=..."></video></figure>
     * 
     * {vimeo_id} == 7th group ($7 in the replacement)
     */
    private function _get_gutenberg_vimeo_embed_pattern($block_name): string
    {
        return '/<figure\s+(?:[^>]*?\s+)?class="' . $block_name . '.*"><video\s+(?:[^>]*?\s+)?src=(["\'])((http[s]?|ftp):\/)?\/?([player.vimeo.com]+)((\/\w+)*\/)([\w\-\]+[^#?\s]+)(.*)?(#[\w\-]+)?<\s*\/\s*video><\s*\/\s*figure>/';
    }

    /**
     * Pattern that matches strings like:
     *
     * <figure class="wp-block-video alignleft"><video controls="" src="https://player.vimeo.com/external/{vimeo_id}?s=..."></video><figcaption>opis wideo</figcaption></figure>
     *
     * {vimeo_id} == 7th group ($7 in the replacement)
     * {caption} == 11th group ($11 in the replacement)
     */
    private function _get_gutenberg_vimeo_embed_with_caption_pattern(string $block_name): string
    {
        return '/<figure\s+(?:[^>]*?\s+)?class="' . $block_name . '.*"><video\s+(?:[^>]*?\s+)?src=(["\'])((http[s]?|ftp):\/)?\/?([player.vimeo.com]+)((\/\w+)*\/)([\w\-\]+[^#?\s]+)(.*)?(#[\w\-]+)?<\s*\/\s*video>((<figcaption>.*<\s*\/\s*figcaption>))?<\s*\/\s*figure>/';
    }
}