<?php
namespace bpmj\wpidea\helpers;

class Video_Length_Formatter
{
    public function to_formatted_string(int $length): string
    {
        return \DateTime::createFromFormat('U', $length)->format('H:i:s');
    }
}
