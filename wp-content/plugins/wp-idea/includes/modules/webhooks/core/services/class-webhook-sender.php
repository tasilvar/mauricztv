<?php

namespace bpmj\wpidea\modules\webhooks\core\services;

use bpmj\wpidea\data_types\Error_Status_Code;
use bpmj\wpidea\events\Event_Name;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\modules\webhooks\core\entities\Webhook;
use bpmj\wpidea\options\Interface_Options;
use bpmj\wpidea\options\Options_Const;
use Exception;
use GuzzleHttp\ClientInterface;

class Webhook_Sender implements Interface_Webhook_Sender
{
    private $http_client;
    private $options;
    private $events;

    public function __construct(
        ClientInterface $http_client,
        Interface_Options $options,
        Interface_Events $events
    )
    {
        $this->http_client = $http_client;
        $this->options = $options;
        $this->events = $events;
    }

    public function send_data(Webhook $webhook, array $data): ?object
    {
        $request_body = json_encode($data);
        $sign = $this->calculate_sign($request_body);
        $response = $this->send_signed_request($webhook->get_url()->get_value(), $sign, $request_body);

        if(!$response){
            return null;
        }

        if(!$this->validate_response($response)){
            $this->events->emit(Event_Name::WEBHOOK_INVALID_RESPONSE_RECEIVED, $webhook, new Error_Status_Code($response->getStatusCode()));
        }

        $this->events->emit(Event_Name::WEBHOOK_HAS_BEEN_CALLED, $webhook, new Error_Status_Code($response->getStatusCode()));

       return json_decode($response->getBody());
    }

    private function send_signed_request(string $url, string $sign, string $request_body): ?object
    {
        try{
            return $this->http_client->post(
                $url,
                [
                    'headers' => [
                        'Content-Type' => 'application/json; charset=utf-8',
                        'x-wpidea-signature' => $sign
                    ],
                    'body' => $request_body,
                    'data_format' => 'body'
                ]
            );
        }catch(Exception $e){
            return null;
        }
    }

    private function validate_response($response): bool
    {
        if(($response->getStatusCode() === 200) || ($response->getStatusCode() === 201)) {
            return true;
        }
        return false;
    }

    private function calculate_sign(string $request_body): string
    {
        return base64_encode(
            hash_hmac('sha256', $request_body, $this->get_api_key(), true)
        );
    }

    private function get_api_key(): ?string
    {
        return $this->options->get(Options_Const::WPI_VALIDATED_KEY);
    }
}
