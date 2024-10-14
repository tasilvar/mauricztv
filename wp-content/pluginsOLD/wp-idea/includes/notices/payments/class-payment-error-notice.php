<?php

namespace bpmj\wpidea\notices\payments;

use bpmj\wpidea\instantiator\Interface_Initiable;
use bpmj\wpidea\events\actions\Interface_Actions;
use bpmj\wpidea\translator\Interface_Translator;

class Payment_Error_Notice implements Interface_Initiable
{
    private $actions;

    private $translator;

    public function __construct(
        Interface_Actions $actions,
        Interface_Translator $translator
    ) {
        $this->actions = $actions;
        $this->translator = $translator;
    }

    public function init(): void
    {
        $this->actions->add('edd_before_checkout_cart', [$this, 'notice']);
    }

    public function notice()
    {
        $message = $_SESSION['cart-error-message'] ?? '';
        if($message) {
            unset($_SESSION['cart-error-message']);
        }

        if ( isset( $_GET['cart-error'] ) ) {
            echo '<p class="payment-error">';

            echo $this->translator->translate('payment_error');

            if (! empty( $message ) ) {
                echo '<br>';
                echo $this->translator->translate('payment_error_details') . ': ' . sanitize_text_field( $message );
            }

            echo '</p>';
        }
    }
}