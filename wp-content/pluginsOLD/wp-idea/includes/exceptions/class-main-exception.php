<?php
namespace bpmj\wpidea\exceptions;
use bpmj\wpidea\translator\Interface_Translator;

abstract class Main_Exception extends \Exception
{
    const MESSAGE = '';
    const CODE = 500;

    public function __construct(Interface_Translator $translator)
    {
        parent::__construct($translator->translate($this::MESSAGE), $this::CODE);
    }
}
