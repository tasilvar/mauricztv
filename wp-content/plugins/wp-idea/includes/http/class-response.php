<?php
namespace bpmj\wpidea\http;

class Response
{
    private $response;
    private $errorMessage = null;

    public function __construct($response)
    {
        $this->response = $response;
        $this->check_for_errors();
    }

    protected function check_for_errors()
    {
        if(is_wp_error($this->response)){
            $this->set_error_message($this->response->get_error_message());
        }
    }

    public function is_error(): bool
    {
        return $this->errorMessage != null;
    }

    public function get_body()
    {
        return wp_remote_retrieve_body( $this->response );
    }

    public function get_decoded_body()
    {
        return json_decode( wp_remote_retrieve_body( $this->response ) );
    }

    public function get_error_message(): ?string
    {
        return $this->errorMessage;
    }

    public function set_error_message(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }





}
