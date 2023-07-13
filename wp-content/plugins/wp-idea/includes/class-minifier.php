<?php

namespace bpmj\wpidea;

use MatthiasMullie\Minify;

class Minifier {

    const EXTENSION_JS = 'js';
    const EXTENSION_CSS = 'css';

    private $paths;
    private $extension;
    private $destination_path;
    private $error_messages = null;

    public function __construct($paths, $destination_path, $extension)
    {
        $this->extension = $extension;

        foreach ($paths as $path){
            $this->add($path);
        }

        $this->add_destination_path($destination_path);
    }

    public function add(string $path): self
    {
        $this->check_if_file_exists($path);
        $this->check_extension($path);

        $this->throw_exception_if_errors();

        $this->paths[] = $path;

        return $this;
    }

    public function minify()
    {
        $this->throw_exception_if_errors();

        $minifier = $this->get_minifier();

        foreach ($this->paths as $path){
            $minifier->add($path);
        }

        try {
            $minifier->minify($this->destination_path);
        } catch (\Exception $e) {
            $this->error_messages[] = $e->getMessage();
            $this->throw_exception_if_errors();
        }
    }

    public function add_destination_path(string $destination_path): self
    {
        $this->destination_path = $destination_path;
        return $this;
    }

    private function get_minifier()
    {
        return $this->extension == self::EXTENSION_JS ? new Minify\JS() : new Minify\CSS();
    }

    private function check_if_file_exists(string $path)
    {
        if(!file_exists($path)){
            $this->error_messages[] = 'File ('.$path.') does  no exist!';
        }
    }

    private function check_extension(string $path)
    {
        $path_info = pathinfo($path);
        if($path_info['extension'] != $this->extension){
            $this->error_messages[] = 'Extension error('.$path_info['extension'].' != '.$this->extension.')!';
        }
    }

    private function throw_exception_if_errors()
    {
        if($this->error_messages) throw new \Exception(implode(",", $this->error_messages));
    }
}
