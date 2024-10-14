<?php
/**
 * This file is licenses under proprietary license
 */

namespace bpmj\wpidea\infrastructure\logs\model;

class Log_Source
{
    public const DEFAULT = 'wpi_default';
    public const ORDERS = 'orders_source';
    public const COMMUNICATION = 'communication';

    public const INVOICE_FAKTUROWNIA = 'wpi_invoices.fakturownia';
    public const INVOICE_IFIRMA = 'wpi_invoices.ifirma';
    public const INVOICE_WFIRMA = 'wpi_invoices.wfirma';
    public const INVOICE_INFAKT = 'wpi_invoices.infakt';
    public const INVOICE_TAXE = 'wpi_invoices.taxe';
}