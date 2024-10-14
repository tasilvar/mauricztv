<?php 
namespace bpmj\wpidea\admin\pages\payments_history;

use bpmj\wpidea\sales\order\Order;
use bpmj\wpidea\events\filters\Interface_Filters;
use bpmj\wpidea\caps\Access_Filter_Name;

class Client_Filter
{
    private $filters;

    public function __construct(Interface_Filters $filters) {
        $this->filters = $filters;
    }

    public function filtered_array(Order $payment): array
    {
        $client = $payment->get_client();
        $client_full_name            = $this->filters->apply(Access_Filter_Name::CUSTOMER_NAME, $client->get_full_name(), $client->get_id());
        $client_email                = $this->filters->apply(Access_Filter_Name::CUSTOMER_EMAIL, $client->get_email(), $client->get_id());
        $client_phone_no             = $client->get_phone_no() ? $this->filters->apply(Access_Filter_Name::CUSTOMER_PHONE, $client->get_phone_no(), $client->get_id()) : '';
        $client_invoice_country      = $payment->get_invoice()->get_invoice_country() ? $this->filters->apply(Access_Filter_Name::CUSTOMER_COUNTRY, $payment->get_invoice()->get_invoice_country(), $client->get_id()) : '';
        $client_invoice_nip          = $payment->get_invoice()->get_invoice_nip() ? $this->filters->apply(Access_Filter_Name::CUSTOMER_NIP, $payment->get_invoice()->get_invoice_nip(), $client->get_id()) : '';
        $client_invoice_company_name = $payment->get_invoice()->get_invoice_company_name() ? $this->filters->apply(Access_Filter_Name::CUSTOMER_COMPANY, $payment->get_invoice()->get_invoice_company_name(), $client->get_id()) : '';
       
        return [
            'full_name' => $client_full_name,
            'user_email' => $client_email,
            'phone_no' => $client_phone_no,
            'invoice_country' => $client_invoice_country,
            'invoice_nip' => $client_invoice_nip,
            'invoice_company_name' => $client_invoice_company_name
        ];
    }

}
