<?php
namespace bpmj\wpidea\modules\affiliate_program\core\value_objects;

use bpmj\wpidea\modules\affiliate_program\api\dto\collections\Partner_External_Landing_Link_DTO_Collection;

final class Partner_Info
{
    private string $partner_id;
    private string $link;
    private Partner_External_Landing_Link_DTO_Collection $external_landing_links;

    public function __construct(
        string $partner_id,
        string $link,
        Partner_External_Landing_Link_DTO_Collection $external_landing_links
    )
    {
        $this->partner_id = $partner_id;
        $this->link = $link;
        $this->external_landing_links = $external_landing_links;
    }

    public function get_partner_id(): string
    {
        return $this->partner_id;
    }

    public function get_affiliate_link(): string
    {
        return $this->link;
    }

    public function get_external_landing_links(): Partner_External_Landing_Link_DTO_Collection
    {
        return $this->external_landing_links;
    }
}