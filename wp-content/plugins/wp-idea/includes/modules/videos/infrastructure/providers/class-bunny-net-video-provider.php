<?php

namespace bpmj\wpidea\modules\videos\infrastructure\providers;

use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;
use bpmj\wpidea\data_types\Url;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Config_Provider;
use bpmj\wpidea\modules\videos\core\providers\Interface_Video_Provider;
use bpmj\wpidea\modules\videos\core\entities\{Video, Video_Collection};
use bpmj\wpidea\modules\videos\core\value_objects\Video_Id;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7;
use PHPUnit\TextUI\Exception;

class Bunny_Net_Video_Provider implements Interface_Video_Provider
{
    private Interface_Video_Config_Provider $config_provider;

    private Client $http_client;

    private const GET_VIDEO_DETAILS_ENDPOINT = ' https://video.bunnycdn.com/library/{libraryId}/videos/{videoId}';
    private const CREATE_VIDEO_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos';
    private const UPLOAD_VIDEO_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos/{videoId}';
    private const FETCH_VIDEO_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos/fetch';
    private const DELETE_VIDEO_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos/{videoId}';
    private const LIST_VIDEOS_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos';
    private const SET_VIDEO_THUMBNAIL_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos/{videoId}/thumbnail';
    private const UPDATE_VIDEO_TITLE_ENDPOINT = 'https://video.bunnycdn.com/library/{libraryId}/videos/{videoId}';

    public function __construct(Bunny_Net_Config_Provider $config_provider, ClientInterface $client)
    {
        $this->config_provider = $config_provider;
        $this->http_client = $client;
    }

    /**
     * @throws GuzzleException
     */
    public function upload_video(string $title, string $source_path): bool
    {
        $create_video_url = str_replace('{libraryId}', $this->get_library_id(), self::CREATE_VIDEO_ENDPOINT);
        $create_video_response = $this->http_client->post($create_video_url, [
            'body' => json_encode([
                'title' => $title
            ]),
            'headers' => $this->get_headers()
        ]);
        $create_video_response_data = json_decode($create_video_response->getBody()->getContents(), true);
        $vendor_id = $create_video_response_data['guid'];

        $upload_video_url = str_replace(
            ['{libraryId}', '{videoId}'],
            [$this->get_library_id(), $vendor_id],
            self::UPLOAD_VIDEO_ENDPOINT
        );
        $upload_video_request = new Request(
            'PUT',
            $upload_video_url,
            $this->get_headers(),
            new Psr7\Stream(fopen($source_path, 'r'))
        );
        $upload_video_response = $this->http_client->send($upload_video_request);

        $upload_video_response_data = json_decode($upload_video_response->getBody()->getContents(), true);

        return $upload_video_response_data['success'];
    }

    /**
     * @throws GuzzleException
     */
    public function upload_video_from_url(Url $video_url): bool
    {
        $fetch_video_url = str_replace('{libraryId}', $this->get_library_id(), self::FETCH_VIDEO_ENDPOINT);
        $fetch_video_response = $this->http_client->post($fetch_video_url, [
            'body' => json_encode([
                'url' => $video_url->get_value()
            ]),
            'headers' => $this->get_headers()
        ]);

        try {
            $fetch_video_response_data = json_decode($fetch_video_response->getBody()->getContents(), true);
            return $fetch_video_response_data['success'];
        } catch (Exception $e) {
        }

        return false;
    }

    /**
     * @throws GuzzleException
     */
    public function set_thumbnail(Video_Id $video_id, Url $thumbnail_url): bool
    {
        $set_thumbnail_url = str_replace(
            ['{libraryId}', '{videoId}'],
            [$this->get_library_id(), $video_id->get_id()],
            self::SET_VIDEO_THUMBNAIL_ENDPOINT
        );

       $set_thumbnail_url .= "?thumbnailUrl=".urlencode($thumbnail_url->get_value());

        $set_thumbnail_response = $this->http_client->post($set_thumbnail_url, [
            'headers' => $this->get_headers()
        ]);

        $set_thumbnail_response_data = json_decode($set_thumbnail_response->getBody(), true);

        return $set_thumbnail_response_data['success'];
    }

    /**
     * @throws GuzzleException
     */
    public function update_video_title(Video_Id $video_id, $title): bool
    {
        $update_video_url = str_replace(
            ['{libraryId}', '{videoId}'],
            [$this->get_library_id(), $video_id->get_id()],
            self::UPDATE_VIDEO_TITLE_ENDPOINT
        );
        $update_video_response = $this->http_client->post($update_video_url, [
            'body' => json_encode([
                'title' => $title
            ]),
            'headers' => $this->get_headers()
        ]);

        $update_video_response_response_data = json_decode($update_video_response->getBody(), true);

        return $update_video_response_response_data['success'];
    }

    /**
     * @throws GuzzleException
     */
    public function delete_video(Video_Id $video_id): bool
    {
        $delete_video_url = str_replace(
            ['{libraryId}', '{videoId}'],
            [$this->get_library_id(), $video_id->get_id()],
            self::DELETE_VIDEO_ENDPOINT
        );
        $delete_video_response = $this->http_client->delete($delete_video_url, [
            'headers' => $this->get_headers()
        ]);

        $delete_video_response_data = json_decode($delete_video_response->getBody(), true);

        return $delete_video_response_data['success'];
    }

    /**
     * @throws GuzzleException
     */
    public function get_video_collections(int $page = 1, int $per_page = 100): Video_Collection
    {
        $get_videos_list_url = str_replace(
            '{libraryId}',
            $this->get_library_id(),
            self::LIST_VIDEOS_ENDPOINT
        );
        $get_videos_list_url .= "?orderBy=date&page={$page}&itemsPerPage={$per_page}";

        $get_videos_list_response = $this->http_client->get($get_videos_list_url, [
            'headers' => $this->get_headers()
        ]);

        $get_videos_list_response_data = json_decode($get_videos_list_response->getBody(), true);

        $video_collection = new Video_Collection();

        foreach ($get_videos_list_response_data['items'] as $video) {
            $video_model = $this->create_model_from_array($video);
            $video_collection->add($video_model);
        }

        return $video_collection;
    }

    /**
     * @throws GuzzleException
     */
    public function count_videos_in_collection(): int
    {
        $get_videos_list_url = str_replace(
            '{libraryId}',
            $this->get_library_id(),
            self::LIST_VIDEOS_ENDPOINT
        );
        $get_videos_list_response = $this->http_client->get($get_videos_list_url, [
            'headers' => $this->get_headers()
        ]);

        $get_videos_list_response_data = json_decode($get_videos_list_response->getBody(), true);
        return $get_videos_list_response_data['totalItems'];
    }

    public function get_video_details(Video_Id $video_id): array
    {
        $video_details_url = str_replace(
            ['{libraryId}', '{videoId}'],
            [$this->get_library_id(), $video_id->get_id()],
            self::DELETE_VIDEO_ENDPOINT
        );

        $video_details_response = $this->http_client->get($video_details_url, [
            'headers' => $this->get_headers()
        ]);

        return json_decode($video_details_response->getBody()->getContents(), true);
    }

    private function get_library_id(): string
    {
        return $this->config_provider->get_configuration()['library_id'];
    }

    private function get_access_key(): string
    {
        return $this->config_provider->get_configuration()['api_key'];
    }

    private function get_headers(): array
    {
        return [
            'AccessKey' => $this->get_access_key(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/*+json'
        ];
    }

    private function create_model_from_array(array $video): Video
    {
        $video_id = new Video_Id($video['guid']);
        $dateUploaded = new \DateTime($video['dateUploaded']);

        return new Video(
            null,
            $video['title'],
            $video_id,
            $video['storageSize'] ?? 0,
            $video['length'],
            $video['width'],
            $video['height'],
            null,
            $dateUploaded
        );
    }

    /**
     * @throws Invalid_Url_Exception
     */
    private function get_bunny_net_video_url(Video_Id $video_id): Url
    {
        $url = "https://video.bunnycdn.com/play/{libraryId}/{videoId}";
        $url = str_replace(['{libraryId}', '{videoId}'], [$this->get_library_id(), $video_id->get_id()], $url);
        return new Url($url);
    }
}