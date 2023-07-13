<?php
namespace bpmj\wpidea\http;

class Http_Client
{
    public function create_request(): Request
    {
        return new Request();
    }

}
