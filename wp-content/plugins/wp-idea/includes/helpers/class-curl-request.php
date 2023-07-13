<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\helpers;

class Curl_Request {

    private $url;
    private $data;

    public function __construct(string $url, array $data)
    {
        $this->url = $url;
        $this->data = $data;
    }

    public function send()
    {
        $payload = json_encode($this->data);

        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload))
        );

        $result = json_decode(curl_exec($ch));
        curl_close($ch);
        return $result;
    }


}
