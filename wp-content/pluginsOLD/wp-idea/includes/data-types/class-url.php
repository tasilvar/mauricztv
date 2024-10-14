<?php
declare(strict_types=1);

namespace bpmj\wpidea\data_types;
use bpmj\wpidea\data_types\exceptions\Invalid_Url_Exception;

class Url
{
    private const PATTERN = "/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i";
    private string $url;

    public function __construct(string $url)
    {
        if (!preg_match(self::PATTERN, $url)) {
            throw new Invalid_Url_Exception();
        }

        $this->url = $url;
    }

    public function get_value(): string
    {
      return $this->url;
    }

    public static function from_string(string $url): self
    {
        return new self($url);
    }
}