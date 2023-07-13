<?php

namespace bpmj\wpidea\admin\video;

use upsell\wp\vimeo\Api;

class Video_Api {
    /**
     * Vimeo API
     *
     * @var Api
     */
    private $vimeo_api;

    public function __construct( $wpi_key, $host ) {
        $this->vimeo_api = new Api( $wpi_key, $host );
    }

    /**
     * Upload file to the remote server
     *
     * @param string $file_url
     * @return mixed
     */
    public function upload_to_remote( $file_url )
    {
        return $this->vimeo_api->uploadVideo( $file_url );
    }

    /**
     * Get video details from the remote server
     *
     * @param int $id
     * @return mixed
     */
    public function get_video( $id )
    {
        try {
            return $this->vimeo_api->getVideo( $id );
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete video from remote
     *
     * @param int $id
     * @return mixed
     */
    public function delete_video( $id )
    {
        try {
            return $this->vimeo_api->deleteVideo( $id );
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get all user's videos
     *
     * @return mixed
     */
    public function get_videos()
    {
        try {
            return $this->vimeo_api->getVideos();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get user data
     *
     * @return mixed
     */
    public function get_user_data()
    {
        try {
            return $this->vimeo_api->getInfo();
        } catch (\Exception $e) {
            return false;
        }
    }
}
