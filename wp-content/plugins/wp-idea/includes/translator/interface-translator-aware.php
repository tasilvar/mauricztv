<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\translator;

interface Interface_Translator_Aware
{
    public function set_translator(Interface_Translator $translator): void;
}
