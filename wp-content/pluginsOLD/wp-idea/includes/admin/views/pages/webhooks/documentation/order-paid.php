<?php
/** @var string $page_title */
/** @var string $type_of_event */
/** @var string $url_webhook_page */
/** @var Interface_Translator $translator */

$event_name = $translator->translate('webhooks.event.'.$type_of_event);
?>

<div class='wrap webhooks-page'>
    <hr class='wp-header-end'>

    <h1 class='wp-heading-inline'><?= $page_title.' - '.$event_name ?></h1>

    <p><?= sprintf( $translator->translate('webhooks.documentation.heading'), $event_name ) ?></p>
    <pre>
    {
      "id": $order_id,
      "status": $order_status,
      "currency": $order_currency,
      "date_completed": $order_date_completed,
      "total": $order_total,
      "payment_method": $order_payment_method,
      "items": [
        {
          "name": $cart_item_name,
          "id": $cart_item_id,
          "price_id": $cart_item_price_id,
          "quantity": $cart_item_quantity,
          "discount": $cart_item_discount,
          "subtotal": $cart_item_subtotal,
          "price": $cart_item_price
        }
      ],
      "customer": {
        "first_name": $client_first_name,
        "last_name": $client_last_name,
        "email": $client_email
      },
      "biling_address": {
        "company_name": $invoice_company_name,
        "tax_id": $invoice_tax_id,
        "street": $invoice_street,
        "building_number": $invoice_building_number,
        "apartment_number": $invoice_apartment_number,
        "postal": $invoice_postal,
        "city": $invoice_city,
        "country_code": $invoice_country_code
      },
      "additional_fields": {
        "buy_as_gift": $additional_buy_as_gift,
        "voucher_codes": $additional_voucher_codes,
        "phone_no": $additional_phone_no,
        "additional_checkbox_checked": $additional_checkbox_checked,
        "additional_checkbox_description": $additional_checkbox_description,
        "additional_checkbox2_checked": $additional_checkbox2_checked,
        "additional_checkbox2_description": $additional_checkbox2_description,
        "order_comment": $additional_order_comment
      }
    }
    </pre>
    <strong>Format JSON</strong>
    <br><br>
    <p><?= $translator->translate('webhooks.documentation.description') ?></p>
        <ul class='webhooks-description'>
            <li><span>$order_id</span>: (int) 1</li>
            <li><span>$order_status</span>: (string) possible values: "Porzucone", "Błędne", "W toku", "Zakończone", "Zwrócone", "Odwołane"</li>
            <li><span>$order_currency</span>: (string) "PLN"</li>
            <li><span>$order_date_completed</span>: (string) "2021-11-30 12:00:00"</li>
            <li><span>$order_total</span>: (float) 20</li>
            <li><span>$order_payment_method</span>: (string) possible values: "manual", "automatic"</li>

            <li><span>$cart_item_name</span>: (string) "Podstawowy z wariantami"</li>
            <li><span>$cart_item_id</span>: (int) 40</li>
            <li><span>$cart_item_price_id</span>: (int) 2</li>
            <li><span>$cart_item_quantity</span>: (int) 1</li>
            <li><span>$cart_item_discount</span>: (int) 0</li>
            <li><span>$cart_item_subtotal</span>: (float) 20</li>
            <li><span>$cart_item_price</span>: (float) 20</li>

            <li><span>$client_first_name</span>: (string) - "Firstname"</li>
            <li><span>$client_last_name</span>: (string) - "Lastname"</li>
            <li><span>$client_email</span>: (string) - "email@test.pl"</li>

            <li><span>$invoice_company_name</span>: (string) "Company name"</li>
            <li><span>$invoice_tax_id</span>: (string) "6422224988"</li>
            <li><span>$invoice_street</span>: (string) "Street name"</li>
            <li><span>$invoice_building_number</span>: (string) "12a"</li>
            <li><span>$invoice_apartment_number</span>: (string) "5"</li>
            <li><span>$invoice_postal</span>: (string) "11-111"</li>
            <li><span>$invoice_city</span>: (string) "City name"</li>
            <li><span>$invoice_country_code</span>: (string) "PL"</li>

            <li><span>$additional_buy_as_gift</span>: (bool) possible values: true, false</li>
            <li><span>$additional_voucher_codes</span>: (string) "PDD14Z7H8NKWR"</li>
            <li><span>$additional_phone_no</span>: (string) "48234643763"</li>
            <li><span>$additional_checkbox_checked</span>: (bool) possible values: true, false</li>
            <li><span>$additional_checkbox_description</span>: (string) "Test checkbox 1"</li>
            <li><span>$additional_checkbox2_checked</span>: (bool) possible values: true, false</li>
            <li><span>$additional_checkbox2_description</span>: (string) "Test checkbox 2"</li>
            <li><span>$additional_order_comment</span>: (string) "Test comment"</li>
        </ul>

            <div class='webhooks-form__footer'>
                <a href='<?= $url_webhook_page ?>' class='webhook-cancel-button'><?= $translator->translate('webhooks.form.return') ?></a>
                <br class='clear' />
            </div>

    
</div>
