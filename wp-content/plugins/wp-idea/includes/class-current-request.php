<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea;

use voku\helper\AntiXSS;

class Current_Request
{
    public const ALLOW_STYLE_ATTRIBUTES = 'allow_style_attributes';
    public const ALLOW_HTML = 'allow_html';

    private $post_vars;
    private $get_vars;
    private $request_vars;
    private $cookie_vars;

    private $request_method;
    private $files;

    public function __construct()
    {
        $this->post_vars = $_POST;
        $this->get_vars = $_GET;
        $this->request_vars = $_REQUEST;
        $this->request_method = $_SERVER['REQUEST_METHOD'] ?? '';
        $this->files = $_FILES;
        $this->cookie_vars = $_COOKIE;
    }

    public static function create(): self
    {
        return new self();
    }

    public function get_user_ip()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } else {
                if (isset($_SERVER['HTTP_X_FORWARDED'])) {
                    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
                } else {
                    if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
                        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
                    } else {
                        if (isset($_SERVER['HTTP_FORWARDED'])) {
                            $ipaddress = $_SERVER['HTTP_FORWARDED'];
                        } else {
                            if (isset($_SERVER['REMOTE_ADDR'])) {
                                $ipaddress = $_SERVER['REMOTE_ADDR'];
                            }
                        }
                    }
                }
            }
        }

        return filter_var($ipaddress, FILTER_VALIDATE_IP);
    }

    public function get_md5_user_ip(): string
    {
        return md5($this->get_user_ip());
    }

    public function query_arg_exists(?string $name = null): bool
    {
        if ($name) {
            return isset($this->get_vars[$name]);
        }
        return ($this->get_vars) ? true : false;
    }

    public function get_query_arg(string $name, array $options = []): ?string
    {
        if (!isset($this->get_vars[$name])) {
            return null;
        }

        return $this->clean_variable($this->get_vars[$name], $options);
    }

    public function cookie_arg_exists(?string $name = null): bool
    {
        if ($name) {
            return isset($this->cookie_vars[$name]);
        }
        return ($this->cookie_vars) ? true : false;
    }

    public function get_cookie_arg(string $name, array $options = []): ?string
    {
        if (!isset($this->cookie_vars[$name])) {
            return null;
        }

        return $this->clean_variable($this->cookie_vars[$name], $options);
    }

    public function delete_cookie_arg(string $name): void
    {
        if (!isset($this->cookie_vars[$name])) {
            return;
        }

        unset($this->cookie_vars[$name]);
        setcookie($name, null, -1, '/');
    }

    public function set_cookie_arg(string $name, string $value, int $expires): void
    {
        $this->cookie_vars[$name] = $value;

        setcookie(
            $name,
            $value,
            $expires,
            '/'
        );
    }

    public function get_query_args(array $options = []): array
    {
        $clean_vars = [];

        foreach ($this->get_vars as $index => $var) {
            $clean_index = $this->clean_variable($index, $options);
            $clean_value = $this->clean_variable($var, $options);

            $clean_vars[$clean_index] = $clean_value;
        }

        return $clean_vars;
    }

    /**
     * @param string $name
     * @param array $options
     * @return array|mixed|string|null
     */
    public function get_body_arg(string $name, array $options = [])
    {
        if (!isset($this->post_vars[$name])) {
            return null;
        }
        return $this->clean_variable($this->post_vars[$name], $options);
    }

    public function get_raw_body_arg(string $name)
    {
        if (!isset($this->post_vars[$name])) {
            return null;
        }
        return $this->post_vars[$name];
    }

    public function request_arg_exists(string $name): bool
    {
        return isset($this->request_vars[$name]);
    }

    /**
     * @return string|array|null
     */
    public function get_request_arg(string $name, array $options = [])
    {
        if (!isset($this->request_vars[$name])) {
            return null;
        }
        return $this->clean_variable($this->request_vars[$name], $options);
    }

    public function clean_variable($variable, array $options = [])
    {
        $antiXss = new AntiXSS();

        if (!in_array(self::ALLOW_HTML, $options)) {
            $variable = $this->strip_tags_for_variables($variable);
        }

        if (in_array(self::ALLOW_STYLE_ATTRIBUTES, $options)) {
            $antiXss->removeEvilAttributes(['style']);
        }
        return $antiXss->xss_clean($variable);
    }

    private function strip_tags_for_variables($variables)
    {
        if (!is_array($variables)) {
            return strip_tags($variables);
        }

        $new_array = [];
        foreach ($variables as $key => $variable) {
            $new_array[strip_tags($key)] = $this->strip_tags_for_variables($variable);
        }
        return $new_array;
    }

    public function get_request_method()
    {
        return $this->request_method;
    }

    /**
     * @return bool|string
     */
    public function get_referer()
    {
        return filter_var($_SERVER['HTTP_REFERER'] ?? '', FILTER_VALIDATE_URL);
    }

    public function get_file(string $name): ?array
    {
        if (!isset($this->files[$name])) {
            return null;
        }

        return $this->files[$name];
    }

    public function get_decoded_raw_post_data(): array
    {
        $data = file_get_contents('php://input');

        $data = $this->clean_variable($data);

        return json_decode($data, true) ?? [];
    }

    public function get_user_agent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public function get_current_page_id(): ?int
    {
        if (!is_singular()) {
            return null;
        }

        return get_the_ID() ?? null;
    }

    public function get_x_requested_with(): ?string
    {
        return $_SERVER['HTTP_X_REQUESTED_WITH'] ?? null;
    }

    public function get_request_uri(): ?string
    {
        return $_SERVER['REQUEST_URI'] ?? null;
    }

    public function get_php_auth_user(): ?string
    {
        return $_SERVER['PHP_AUTH_USER'] ?? null;
    }

    public function get_php_auth_pw(): ?string
    {
        return $_SERVER['PHP_AUTH_PW'] ?? null;
    }
}
