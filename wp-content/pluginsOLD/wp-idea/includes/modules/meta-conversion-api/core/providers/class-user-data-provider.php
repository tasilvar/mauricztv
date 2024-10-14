<?php

namespace bpmj\wpidea\modules\meta_conversion_api\core\providers;

use bpmj\wpidea\Current_Request;
use bpmj\wpidea\modules\meta_conversion_api\core\io\Interface_Cookie_Based_Data_Provider;
use bpmj\wpidea\sales\order\client\Client;
use bpmj\wpidea\sales\order\invoice\Invoice;
use bpmj\wpidea\user\Interface_Current_User_Getter;
use FacebookAds\Object\ServerSide\UserData;

class User_Data_Provider implements Interface_User_Data_Provider
{
    private Interface_Cookie_Based_Data_Provider $cookie_based_data_provider;
    private Current_Request $current_request;
    private Interface_Current_User_Getter $current_user_getter;

    public function __construct(
        Interface_Cookie_Based_Data_Provider $cookie_based_data_provider,
        Current_Request $current_request,
        Interface_Current_User_Getter $current_user_getter
    ) {
        $this->cookie_based_data_provider = $cookie_based_data_provider;
        $this->current_request = $current_request;
        $this->current_user_getter = $current_user_getter;
    }

    public function get_user_data_for_logged_user(): UserData
    {
        $user_data = (new UserData())
            ->setClientIpAddress($this->current_request->get_user_ip())
            ->setClientUserAgent($this->current_request->get_user_agent());

        $fbp = $this->cookie_based_data_provider->get_fbp();
        if ($fbp) {
            $user_data->setFbp($fbp);
        }

        $fbc = $this->cookie_based_data_provider->get_fbc();
        if ($fbc) {
            $user_data->setFbc($fbc);
        }

        $user = $this->current_user_getter->get();

        if ($user) {
            $user_data->setEmail($user->get_email());
            $user_data->setFirstName($user->get_first_name());
            $user_data->setLastName($user->get_last_name());
        }

        return $user_data;
    }

    public function get_user_data(?Client $client = null, ?Invoice $invoice = null): UserData
    {
        $user_data = (new UserData())
            ->setClientIpAddress($this->current_request->get_user_ip())
            ->setClientUserAgent($this->current_request->get_user_agent());

        $fbp = $this->cookie_based_data_provider->get_fbp();
        if ($fbp) {
            $user_data->setFbp($fbp);
        }

        $fbc = $this->cookie_based_data_provider->get_fbc();
        if ($fbc) {
            $user_data->setFbc($fbc);
        }

        if ($client) {
            $user_data->setEmail($client->get_email());
            $user_data->setFirstName($client->get_first_name());
            $user_data->setLastName($client->get_last_name());

            $phone = $client->get_phone_no();
            if ($phone) {
                $user_data->setPhone($phone);
            }
        }

        if ($invoice) {
            $city = $invoice->get_invoice_city();
            if ($city) {
                $user_data->setCity($city);
            }

            $postcode = $invoice->get_invoice_postcode();
            if ($postcode) {
                $user_data->setZipCode($postcode);
            }

            $country = $invoice->get_invoice_country();
            if ($country) {
                $user_data->setCountryCode($country);
            }
        }

        return $user_data;
    }
}
