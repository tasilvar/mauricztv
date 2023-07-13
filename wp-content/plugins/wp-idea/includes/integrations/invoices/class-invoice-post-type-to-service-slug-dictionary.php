<?php
/**
 * This file is licenses under proprietary license
 */

declare(strict_types=1);

namespace bpmj\wpidea\integrations\invoices;

use BPMJ_WP_Fakturownia;
use BPMJ_WP_iFirma;
use BPMJ_WP_wFirma;
use BPMJ_WP_Infakt;
use BPMJ_WP_Taxe;

class Invoice_Post_Type_To_Service_Slug_Dictionary
{
    public const DIC = [
        'bpmj_wp_fakturownia' => 'wp-fakturownia',
        'bpmj_wp_ifirma' => 'wp-ifirma',
        'bpmj_wp_wfirma' => 'wp-wfirma',
        'bpmj_wp_infakt' => 'wp-infakt',
        'bpmj_wp_taxe' => 'wp-taxe'
    ];
}