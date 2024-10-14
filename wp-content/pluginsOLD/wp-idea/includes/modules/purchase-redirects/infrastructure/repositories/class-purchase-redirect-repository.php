<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\infrastructure\repositories;

use bpmj\wpidea\modules\purchase_redirects\core\repositories\Interface_Purchase_Redirect_Repository;
use bpmj\wpidea\options\Interface_Options;

class Purchase_Redirect_Repository implements Interface_Purchase_Redirect_Repository
{
    private const PURCHASE_REDIRECT = 'wpi_purchase_redirections';
    private Interface_Options $options;

    public function __construct(Interface_Options $options) {
        $this->options = $options;
    }

    public function get_redirections_in_array(): array
    {
        $redirections = $this->options->get(self::PURCHASE_REDIRECT);

        if(!is_array($redirections)){
          return [];
        }

        return $this->options->get(self::PURCHASE_REDIRECT);
    }

    public function update(array $url_redirects): void
    {
        $this->options->set(self::PURCHASE_REDIRECT, $url_redirects);
    }
}