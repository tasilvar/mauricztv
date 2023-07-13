<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\sales\product\model;

class Product_Mailers_Settings
{
    private array $mailchimp_lists;
    private array $mailerlite_lists;
    private array $freshmail_lists;
    private string $ipresso_tags;
    private string $ipresso_tags_unsubscribe;
    private array $activecampaign_lists;
    private array $activecampaign_lists_unsubscribe;
    private string $activecampaign_tags;
    private string $activecampaign_tags_unsubscribe;
    private array $getresponse_lists;
    private array $getresponse_lists_unsubscribe;
    private array $getresponse_tags;
    private string $salesmanago_tags;
    private array $interspire_lists;
    private array $convertkit_lists;
    private array $convertkit_tags_unsubscribe;
    private array $convertkit_tags;

    public function __construct(
        array $mailchimp_lists = [],
        array $mailerlite_lists = [],
        array $freshmail_lists = [],
        string $ipresso_tags = '',
        string $ipresso_tags_unsubscribe = '',
        array $activecampaign_lists = [],
        array $activecampaign_lists_unsubscribe = [],
        string $activecampaign_tags = '',
        string $activecampaign_tags_unsubscribe = '',
        array $getresponse_lists = [],
        array $getresponse_lists_unsubscribe = [],
        array $getresponse_tags = [],
        string $salesmanago_tags = '',
        array $interspire_lists = [],
        array $convertkit_lists = [],
        array $convertkit_tags = [],
        array $convertkit_tags_unsubscribe = []
    ) {
        $this->mailchimp_lists = $mailchimp_lists;
        $this->mailerlite_lists = $mailerlite_lists;
        $this->freshmail_lists = $freshmail_lists;
        $this->ipresso_tags = $ipresso_tags;
        $this->ipresso_tags_unsubscribe = $ipresso_tags_unsubscribe;
        $this->activecampaign_lists = $activecampaign_lists;
        $this->activecampaign_lists_unsubscribe = $activecampaign_lists_unsubscribe;
        $this->activecampaign_tags = $activecampaign_tags;
        $this->activecampaign_tags_unsubscribe = $activecampaign_tags_unsubscribe;
        $this->getresponse_lists = $getresponse_lists;
        $this->getresponse_lists_unsubscribe = $getresponse_lists_unsubscribe;
        $this->getresponse_tags = $getresponse_tags;
        $this->salesmanago_tags = $salesmanago_tags;
        $this->interspire_lists = $interspire_lists;
        $this->convertkit_lists = $convertkit_lists;
        $this->convertkit_tags = $convertkit_tags;
        $this->convertkit_tags_unsubscribe = $convertkit_tags_unsubscribe;
    }

    public function get_mailchimp_lists(): array
    {
        return $this->mailchimp_lists;
    }

    public function get_mailerlite_lists(): array
    {
        return $this->mailerlite_lists;
    }

    public function get_freshmail_lists(): array
    {
        return $this->freshmail_lists;
    }

    public function get_ipresso_tags(): string
    {
        return $this->ipresso_tags;
    }

    public function get_ipresso_tags_unsubscribe(): string
    {
        return $this->ipresso_tags_unsubscribe;
    }

    public function get_activecampaign_lists(): array
    {
        return $this->activecampaign_lists;
    }

    public function get_activecampaign_lists_unsubscribe(): array
    {
        return $this->activecampaign_lists_unsubscribe;
    }

    public function get_activecampaign_tags(): string
    {
        return $this->activecampaign_tags;
    }

    public function get_activecampaign_tags_unsubscribe(): string
    {
        return $this->activecampaign_tags_unsubscribe;
    }

    public function get_getresponse_lists(): array
    {
        return $this->getresponse_lists;
    }

    public function get_getresponse_lists_unsubscribe(): array
    {
        return $this->getresponse_lists_unsubscribe;
    }

    public function get_getresponse_tags(): array
    {
        return $this->getresponse_tags;
    }

    public function get_salesmanago_tags(): string
    {
        return $this->salesmanago_tags;
    }

    public function get_interspire_lists(): array
    {
        return $this->interspire_lists;
    }

    public function get_convertkit_lists(): array
    {
        return $this->convertkit_lists;
    }

    public function get_convertkit_tags(): array
    {
        return $this->convertkit_tags;
    }

    public function get_convertkit_tags_unsubscribe(): array
    {
        return $this->convertkit_tags_unsubscribe;
    }
}