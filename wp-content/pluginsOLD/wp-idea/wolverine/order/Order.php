<?php
namespace bpmj\wpidea\wolverine\order;

class Order
{
    const EVENT_ON_ORDER_COMPLETE = 'order_complete';

    const ORDER_NOT_INITIALIZED = 'Order is not initialized';

    protected $payment;

    public function __construct($order_id = null)
    {
        $this->payment = new \EDD_Payment($order_id);
    }

    public function addProduct($product, $variant = null)
    {
        $this->checkIfOrderIsInitialized();

        $args = [];
        if(!empty($variant)) {
            $args = [
                'price_id' => $variant->getId()
            ];
        }
        $this->payment->add_download($product->getId(), $args);
    }

    public function setEmail($email)
    {
        $this->checkIfOrderIsInitialized();
        $this->payment->email = $email;
    }

    public function setTotal($total)
    {
        $this->checkIfOrderIsInitialized();
        $this->payment->total = $total;
    }

    public function markAsCompletedAndSave()
    {
        $this->checkIfOrderIsInitialized();

        $this->payment->status = 'pending';
        $this->payment->save();

        // has to be splitted to two operations to not generating error
        $this->payment->status = 'complete';
        $this->payment->save();
    }

    public function getId()
    {
        $this->checkIfOrderIsInitialized();
        return $this->payment->ID;
    }

    private function checkIfOrderIsInitialized()
    {
        if (empty($this->payment)) {
            throw new \Exception(self::ORDER_NOT_INITIALIZED);
        }
    }

    public function getFirstName()
    {
        return $this->payment->first_name;
    }

    public function getLastName()
    {
        return $this->payment->last_name;
    }

    public function getEmail()
    {
        return $this->payment->email;
    }

    public function getCartDetails()
    {
        return $this->payment->cart_details;
    }

    public function getStatus()
    {
        return $this->payment->status;
    }

    public function getCurrency()
    {
        return $this->payment->currency;
    }

    public function getCompletedDate()
    {
        return $this->payment->completed_date;
    }

    public function getTotal()
    {
        return $this->payment->total;
    }

    public function getGateway()
    {
        return $this->payment->gateway;
    }

    public function getBillingAddress()
    {
        $company_name = $this->getPaymentMeta("bpmj_edd_invoice_company_name");
        $person_name = $this->getPaymentMeta("bpmj_edd_invoice_person_name");

        if(!$person_name && !$company_name){
            return [];
        }

        if($person_name){
            $data['person_name'] = $person_name;
        }

        if($company_name){
            $data['company_name'] = $company_name;
            $data['tax_id'] = $this->getPaymentMeta("bpmj_edd_invoice_nip");
        }

        $data['street']  = $this->getPaymentMeta("bpmj_edd_invoice_street");
        $data['postal']  = $this->getPaymentMeta("bpmj_edd_invoice_postcode");
        $data['city'] = $this->getPaymentMeta("bpmj_edd_invoice_city");
        $data['country_code'] = $this->getPaymentMeta("bpmj_edd_invoice_country");

        return $data;
    }

    private function getPaymentMeta($name)
    {
        return $this->payment->payment_metas[$name] ?? null;
    }

    public function getVoucherCodes()
    {
       return edd_get_payment_meta( $this->payment->ID, 'bpmj_eddcm_buy_as_gift_vouchers', true );
    }

    public function getAdditionalFields()
    {
        return edd_get_payment_meta( $this->payment->ID, 'bpmj_eddcm_purchase_data', true );
    }

}
