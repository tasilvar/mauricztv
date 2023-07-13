<?php
namespace bpmj\wpidea\filters;

use bpmj\wpidea\User_Security;

class Filter_CSRF_IP implements Interface_Filter
{
    private const DEFAULT_PRIORITY = 10;
    private const ACCEPTED_ARGS = 1;

    private $ip;

    public function get_tag(): string
    {
        return User_Security::SKIP_CSRF_ACCESS_MD5_IPS_FILTER;
    }

    public function get_function(): callable
    {
        return [$this, 'do_filter'];
    }

    public function get_priority(): int
    {
        return self::DEFAULT_PRIORITY;
    }

    public function get_accepted_args(): int
    {
        return self::ACCEPTED_ARGS;
    }

    public function is_valid(): bool
    {
        return isset($this->ip);
    }

    public function set_ip(string $ip): bool
    {
        if(!$this->is_ip_valid($ip)) {
            return false;
        }

        $this->ip = $ip;

        return true;
    }

    protected function is_ip_valid(string $ip): bool
    {
        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    public function do_filter(array $md5_ips): array
    {
        if($this->ip) {
            $md5_ips[] = md5($this->ip);
        }

        return $md5_ips;
    }
}