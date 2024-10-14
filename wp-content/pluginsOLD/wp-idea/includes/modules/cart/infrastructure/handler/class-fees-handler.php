<?php

namespace bpmj\wpidea\modules\cart\infrastructure\handler;

use bpmj\wpidea\modules\cart\core\handler\Interface_Fees_Handler;
use bpmj\wpidea\modules\cart\core\entities\Fee;
use bpmj\wpidea\modules\cart\core\collections\Fee_Collection;
use bpmj\wpidea\modules\cart\api\Fees_API;

class Fees_Handler implements Interface_Fees_Handler
{

    public function add_fee(Fee $fee): void
    {
        $args = [
            'amount' => $fee->get_amount(),
            Fees_API::NET_AMOUNT_FEE_INDEX => $fee->get_net_amount(),
            'label' => $fee->get_label(),
            Fees_API::FEE_ID_FEE_INDEX => $fee->get_id(),
            Fees_API::TAX_RATE_FEE_INDEX => $fee->get_tax_rate()
        ];

        EDD()->fees->add_fee($args);
    }

    public function remove_fee($id): void
    {
        $fees = $this->get_edd_fees();

        if(empty($fees) || !is_array($fees)) {
            return;
        }

        if ( !isset( $fees[ $id ] ) ) {
            return;
        }

        unset( $fees[ $id ] );
        EDD()->session->set( 'edd_cart_fees', $fees );
    }

    public function get_fee($id): ?Fee
    {
        $edd_fee = $this->get_edd_fees()[$id] ?? null;

        if(!$edd_fee) {
            return null;
        }

        /** @var array $edd_fee */
        $edd_fee[Fees_API::FEE_ID_FEE_INDEX] = $id;
        return $this->create_fee_from_edd_fee($edd_fee);
    }

    public function get_fees(): Fee_Collection
    {
        $edd_fees = $this->get_edd_fees();
        $objects = [];

        foreach ($edd_fees as $id => $edd_fee) {
            $edd_fee[Fees_API::FEE_ID_FEE_INDEX] = $id;

            $objects[] = $this->create_fee_from_edd_fee($edd_fee);
        }

        return Fee_Collection::create_from_array($objects);
    }

    private function create_fee_from_edd_fee(array $edd_fee): Fee
    {
        return Fee::create(
            $edd_fee[Fees_API::FEE_ID_FEE_INDEX],
            $edd_fee['label'],
            $edd_fee['amount'],
            $edd_fee[Fees_API::NET_AMOUNT_FEE_INDEX] ?? $edd_fee['amount'],
            $edd_fee[Fees_API::TAX_RATE_FEE_INDEX] ?? 0
        );
    }

    private function get_edd_fees(): array
    {
        $edd_fees = EDD()->session->get('edd_cart_fees');
        return is_array($edd_fees) ? $edd_fees : [];
    }
}