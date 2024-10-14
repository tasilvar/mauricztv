<?php
namespace bpmj\wpidea\http;

class Request
{
    public const METHOD_POST = 'POST';
    public const METHOD_GET = 'GET';

    private $url;
    private $timout = 15;
    private $params = [];
    private $headers = [];
    private $response;
    private $method = self::METHOD_GET;
    private $sslverify = false;

    public function get_headers(): array
    {
        return $this->headers;
    }

    public function set_headers(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }

    public function get_url()
    {
        if($this->method == self::METHOD_GET){
            return add_query_arg( $this->params, $this->url );
        }

        return $this->url;
    }

    public function set_url($url): self
    {
        $this->url = $url;
        return $this;
    }

    public function get_timout()
    {
        return $this->timout;
    }

    public function set_timout($timout): self
    {
        $this->timout = $timout;
        return $this;
    }

    public function get_params(): array
    {
        return $this->params;
    }

    public function set_params(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    public function is_sslverify(): bool
    {
        return $this->sslverify;
    }

    public function set_sslverify(bool $sslverify): self
    {
        $this->sslverify = $sslverify;
        return $this;
    }

    public function get_response()
    {
        return $this->response;
    }

    public function set_response($response): self
    {
        $this->response = $response;
        return $this;
    }

    public function get_method()
    {
        return $this->method;
    }

    public function set_method($method): self
    {
        $this->method = $method;
        return $this;
    }

    public function add_param(string $key, ?string $param)
    {
        $this->params[$key] = $param;
        return $this;
    }

    public function add_header(string $key, ?string $param)
    {
        $this->headers[$key] = $param;
        return $this;
    }

    public function get_args(): array
    {
        $args = [
            'timeout' => $this->get_timout(),
            'sslverify' => $this->is_sslverify()
        ];

        if($this->params && $this->method == self::METHOD_POST){
            $args['body'] = json_encode(  $this->params );
        }

        if($this->headers){
            $args['headers'] = $this->headers;
        }

        return $args;
    }

    public function send($method = null): ?Response
    {
        $response = null;
        if($method){
            $this->method = $method;
        }

        if($this->method == self::METHOD_POST){
            $response = wp_remote_post( $this->get_url(), $this->get_args());
        } else if($this->method == self::METHOD_GET){
            $response = wp_remote_get( $this->get_url(), $this->get_args());
        }
        $this->response =  new Response($response);

        return $this->response;
    }




}
