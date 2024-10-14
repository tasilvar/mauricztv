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

class Invoice_Service_Name_To_Class_Dictionary
{
    public const DIC = [
        'wp-fakturownia' => BPMJ_WP_Fakturownia::class,
        'wp-ifirma' => BPMJ_WP_iFirma::class,
        'wp-wfirma' => BPMJ_WP_wFirma::class,
        'wp-infakt' => BPMJ_WP_Infakt::class,
        'wp-taxe' => BPMJ_WP_Taxe::class
    ];
}