<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\purchase_redirects\core\repositories;

interface Interface_Purchase_Redirect_Repository
{
    public function get_redirections_in_array(): array;

    public function update(array $url_redirects): void;
}