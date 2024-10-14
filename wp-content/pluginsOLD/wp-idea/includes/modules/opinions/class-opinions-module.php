<?php

namespace bpmj\wpidea\modules\opinions;

use bpmj\wpidea\modules\opinions\api\controllers\Opinions_Ajax_Controller;
use bpmj\wpidea\modules\opinions\api\Opinions_API;
use bpmj\wpidea\modules\opinions\api\Opinions_API_Static_Helper;
use bpmj\wpidea\modules\opinions\core\providers\Interface_Opinions_Config_Provider;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;

class Opinions_Module implements Interface_Module
{

    private Interface_Opinions_Config_Provider $opinions_config_provider;
    private Opinions_API $opinions_api;

    public function __construct(
        Interface_Opinions_Config_Provider $opinions_config_provider,
        Opinions_API $opinions_api
    ) {
        $this->opinions_config_provider = $opinions_config_provider;
        $this->opinions_api = $opinions_api;
    }

	public function get_routes(): array
	{
		return [
			'admin/opinions_ajax' => Opinions_Ajax_Controller::class
		];
	}

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'opinions.menu_title' => 'Opinie',
                'opinions.page_title' => 'Opinie',
                'opinions.column.product_name' => 'Nazwa produktu',
                'opinions.column.user_name' => 'Imię i nazwisko',
                'opinions.column.user_email' => 'Email',
                'opinions.column.opinion_rating' => 'Ocena',
                'opinions.column.opinion_content' => 'Treść',
                'opinions.column.date_of_issue' => 'Data wystawienia',
                'opinions.column.status' => 'Status',
                'opinions.status.waiting' => 'Oczekująca',
                'opinions.status.accepted' => 'Zaakceptowana',
                'opinions.status.discarded' => 'Odrzucona',
                'opinions.action.accept_opinion' => 'Akceptuj',
                'opinions.action.discard_opinion' => 'Odrzuć',
                'opinions.add_opinion_form.success.rate_next' => 'Opinia o "%s" została dodana i oczekuje na zatwierdzenie przez administratora. Możesz teraz ocenić kolejny produkt.',
                'opinions.add_opinion_form.success' => 'Opinia o "%s" została dodana i oczekuje na zatwierdzenie przez administratora.',
                'opinions.add_opinion_form.error.product_already_rated' => 'Wybrany produkt został już oceniony',
                'opinions.add_opinion_form.error.no_access_to_product' => 'Nie masz dostępu do wybranego produktu',
                'opinions.add_opinion_form.error.no_user_found' => 'Aby dodać opinię musisz być zalogowany',
                'opinions.add_opinion_form.error.no_product_found' => 'Wystąpił błąd. Skontaktuj się z administratorem.',
                'opinions.menu_title.waiting_opinions' => 'Oczekujące opinie',
            ],
            'en_US' => [
                'opinions.menu_title' => 'Opinions',
                'opinions.page_title' => 'Opinions',
                'opinions.column.product_name' => 'Product name',
                'opinions.column.user_name' => 'First name and last name',
                'opinions.column.user_email' => 'Email',
                'opinions.column.opinion_rating' => 'Opinion rating',
                'opinions.column.opinion_content' => 'Opinion content',
                'opinions.column.date_of_issue' => 'Date of issue',
                'opinions.column.status' => 'Status',
                'opinions.status.waiting' => 'Waiting',
                'opinions.status.accepted' => 'Accepted',
                'opinions.status.discarded' => 'Discarded',
                'opinions.action.accept_opinion' => 'Accept',
                'opinions.action.discard_opinion' => 'Discard',
                'opinions.add_opinion_form.success.rate_next' => 'The opinion for "%s" has been added and is pending approval by the administrator. You can now rate another product.',
                'opinions.add_opinion_form.success' => 'The opinion for "%s" has been added and is pending approval by the administrator.',
                'opinions.add_opinion_form.error.product_already_rated' => 'Chosen product has already been rated',
                'opinions.add_opinion_form.error.no_access_to_product' => 'You have no access to chosen product',
                'opinions.add_opinion_form.error.no_user_found' => 'To add a review you must be logged in',
                'opinions.add_opinion_form.error.no_product_found' => 'An error occurred. Please contact the administrator.',
                'opinions.menu_title.waiting_opinions' => 'Waiting opinions',
            ],
        ];
    }

    public function init(): void
    {
        Opinions_API_Static_Helper::init($this->opinions_api);

        if (!$this->opinions_config_provider->is_enabled()) {
           return;
        }
    }
}