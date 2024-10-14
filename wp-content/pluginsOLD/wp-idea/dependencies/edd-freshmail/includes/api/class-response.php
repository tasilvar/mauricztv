<?php

namespace bpmj\wp\eddfm\api;

/**
 * API call's response
 *
 * @author Pawel
 */
class Response {

    /**
     * Status message (ie. 'success')
     * @var string
     */
    protected $status;

    /**
     * Response data
     * @var array
     */
    protected $data;

    protected $message;

    /**
     * Response raw data (including status, data, errors etc.)
     * @var array
     */
    protected $raw_data;

    /**
     * Response errors
     * @var array
     */
    protected $errors;

    /**
     * Setup the object from JSON
     *
     * @param string $json
     */
    public function __construct($json) {
        $data = json_decode($json, true);
        $this->raw_data = $data;
        $this->status = $data['status'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->data = $data['data'] ?? [];
        $this->errors = $data['errors'] ?? [];
    }

    /**
     * Get response code
     *
     * @return int
     */
    public function get_code() {
        return $this->code;
    }

    /**
     * Get response status
     *
     * @return string
     */
    public function get_status() {
        return $this->status;
    }

    /**
     * Get response description
     *
     * @return string
     */
    public function get_message() {
        return $this->message;
    }

    /**
     * Get response data (either full or the specified field)
     *
     * @param string $key
     * @return array
     */
    public function get_data($key = null) {
        if ($key) {
            return empty($this->data[$key]) ? null : $this->data[$key];
        }
        return $this->data;
    }

    /**
     * Get response errors
     *
     * @return array
     */
    public function get_errors() {
        return $this->errors;
    }

    /**
     * Get response errors as string - one error message per line
     *
     * @return string
     */
    public function get_errors_as_string() {
        $errors = array();
        foreach ($this->get_errors() as $error) {
            $errors[] = "[{$error['code']}]: {$error['message']}";
        }
        return implode("\n", $errors);
    }

    /**
     * Get response raw data (either full or the specified field)
     *
     * @param string $key
     * @return array
     */
    public function get_raw_data($key = null) {
        if ($key) {
            return empty($this->raw_data[$key]) ? null : $this->raw_data[$key];
        }
        return $this->raw_data;
    }

    /**
     * Is the response successful
     *
     * @return bool
     */
    public function is_success() {
        return 'OK' === $this->get_status();
    }

}
