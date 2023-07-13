<?php

namespace bpmj\wpidea\modules\increasing_sales\core\services;

class Increasing_Sales
{
    private Offer_Renderer $offer_renderer;

    public function __construct(
        Offer_Renderer $offer_renderer
    ) {
        $this->offer_renderer = $offer_renderer;
    }

    public function render_offers(): string
    {
        return $this->offer_renderer->get_offers_html();
    }
}