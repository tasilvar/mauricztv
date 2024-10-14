<?php

namespace upsell\wp\vimeo;

class Api
{
    const HEADER_WPI_KEY = 'wpi_key';
    const HEADER_HOST_NAME = 'host_name';
    const AUTH_HEADER_NAME = 'authorization';

    protected $wpIdeaKey = null;

    protected $apiURL = 'https://a.idealms.io';

    protected $hostName;

    /**
     * @param string $wpIdeaKey WP Idea valid api key
     * @param string $hostName Client host name (required for the api key validation)
     */
    public function __construct($wpIdeaKey, $hostName)
    {
        $this->wpIdeaKey = $wpIdeaKey;
        $this->hostName = $hostName;
    }

    /**
     * @param $url
     * @param array $params
     * @param string $method
     * @return bool|string
     */
    protected function makeRequest($url, $params = [], $method = 'GET')
    {
        $curlOptions = [
            CURLOPT_URL => $this->apiURL . $url,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => [
                self::AUTH_HEADER_NAME . ': ' . $this->_getSerializedAuthHeaders()
            ],
        ];

        if ('POST' === $method) {
            $curlOptions[CURLOPT_POST] = true;
            $curlOptions[CURLOPT_POSTFIELDS] = $params;
        }

        if ('DELETE' === $method)
            $curlOptions[CURLOPT_CUSTOMREQUEST] = 'DELETE';

        $ch = curl_init();
        curl_setopt_array($ch, $curlOptions);
        $response = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (200 !== $statusCode)
            throw new \Exception($response);

        curl_close($ch);

        return $response;
    }

    /**
     * Returns auth headers (wpi key and host name) encoded to json
     *
     * @return string
     */
    private function _getSerializedAuthHeaders()
    {
        $headers = [
            self::HEADER_WPI_KEY => $this->wpIdeaKey,
            self::HEADER_HOST_NAME => $this->hostName
        ];

        return json_encode( $headers );
    }

    public function getInfo()
    {
        $response = $this->makeRequest('/api/user');

        $arr_response = $this->getArrayResponse($response);
        if (! isset($arr_response['id']))
            return 'Something went wrong with getting info';

        return $arr_response;
    }

    public function getVideos()
    {
        $response = $this->makeRequest('/api/user/videos');

        $arr_response = $this->getArrayResponse($response);
        if (! is_array($arr_response))
            return 'Something went wrong with getting info';

        if (empty($arr_response))
            return 'You have no videos';

        return $arr_response;
    }

    public function uploadVideo($url)
    {
        $response = $this->makeRequest('/api/user/videos', ['link' => $url], 'POST');

        $arr_response = $this->getArrayResponse($response);
        if (! isset($arr_response['vimeo_id']))
            return 'Video has not been sent';

        return $arr_response;
    }

    public function getVideo($id)
    {
        $response = $this->makeRequest('/api/user/videos/' . $id);

        $arr_response = $this->getArrayResponse($response);
        if (! isset($arr_response['id']))
            return 'Video with this ID does not exists';

        return $arr_response;
    }

    public function deleteVideo($id)
    {
        $response = $this->makeRequest('/api/user/videos/' . $id, [], 'DELETE');

        if ('1' !== $response)
            return 'Video was not deleted';

        return $response;
    }

    public function setUrl($url)
    {
        $this->apiURL = $url;
    }

    private function getArrayResponse($response)
    {
        return json_decode($response, true);
    }
}
