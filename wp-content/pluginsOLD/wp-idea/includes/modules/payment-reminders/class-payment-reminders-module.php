<?php

namespace bpmj\wpidea\modules\payment_reminders;

use bpmj\wpidea\admin\settings\Settings_Const;
use bpmj\wpidea\admin\subscription\models\Subscription;
use bpmj\wpidea\admin\subscription\models\Subscription_Const;
use bpmj\wpidea\infrastructure\scheduler\Interface_Scheduler;
use bpmj\wpidea\modules\payment_reminders\core\jobs\Send_Reminders_Job;
use bpmj\wpidea\modules\payment_reminders\core\services\Options;
use bpmj\wpidea\settings\Interface_Settings;
use bpmj\wpidea\shared\abstractions\modules\Interface_Module;
use Psr\Container\ContainerInterface;

class Payment_Reminders_Module implements Interface_Module
{
    private ContainerInterface $container;
    private Interface_Scheduler $scheduler;
    private Subscription $subscription;
    private Options $options;

    public function __construct(
        ContainerInterface $container,
        Interface_Scheduler $scheduler,
        Subscription $subscription,
        Options $options
    )
    {
        $this->container = $container;
        $this->scheduler = $scheduler;
        $this->subscription = $subscription;
        $this->options = $options;
    }

    public function init(): void
    {
        $job = $this->container->get(Send_Reminders_Job::class);
        if(!$this->is_active()) {
            $this->scheduler->unschedule($job);
            return;
        }

        $this->scheduler->schedule($job);
    }

    private function is_active(): bool
    {
        return $this->subscription->get_plan() === Subscription_Const::PLAN_PRO &&
            $this->options->get(Settings_Const::PAYMENT_REMINDERS_ENABLED);
    }

    public function get_routes(): array
    {
        return [
        ];
    }

    public function get_translations(): array
    {
        return [
            'pl_PL' => [
                'settings.messages.payment_reminders' => 'Włącz odzyskiwanie utraconych zamówień',
                'settings.messages.payment_reminders.notice' => 'Zmień pakiet: Aby korzystać z funkcjonalności odzyskiwania utraconych koszyków musisz zmienić swoją licence na PRO.',
                'settings.messages.payment_reminders.desc' => 'Włącza mechanizm wysyłający wiadomość do użytkowników, którzy nie dokonali płatności.',
                'settings.messages.payment_reminders.number_days' => 'Ilość dni, po których wysyłana jest wiadomość w przypadku braku płatności',
                'settings.messages.payment_reminders.message_subject' => 'Temat wiadomości wysyłanej w przypadku braku płatności',
                'settings.messages.payment_reminders.message_subject.default' => 'Masz nieopłacone zamówienie nr {payment_id}',
                'settings.messages.payment_reminders.message_content' => 'Treść wiadomości wysyłanej w przypadku braku płatności',
                'settings.messages.payment_reminders.message_content.default' => 'Dzień dobry {name},
                                                                        W dniu {date} zostało złożone zamówienie z obowiązkiem zapłaty na poniższe produkty:
                                                                        {product_list}
                                                                        
                                                                        Do dnia dzisiejszego nie zostało ono przez Ciebie opłacone.
                                                                        By dokończyć płatność, dokonaj proszę przelewu kwoty {amount} na poniższe dane:
                                                                        Nazwa firmy
                                                                        ul. Nazwa ulicy 1
                                                                        00-000 Miasto
                                                                        Nr konta bankowego: 00 0000 0000 0000 0000 0000 0000
                                                                        W tytule przelewu wpisz: Płatność za zamówienie nr {payment_id}
                                                                        
                                                                        Pozdrawiamy,
                                                                        Zespół {sitename}',
                'settings.messages.payment_reminders.message_content.desc' => 'Wpisz treść wiadomości wysyłanej w przypadku braku płatności. W treści można używać znaczników HTML oraz korzystać z poniższych tagów:
                                                                        %1$s %2$s{name} %3$s -  imię kupującego
                                                                        %1$s %2$s{date}%3$s - data dokonania zakupu
                                                                        %1$s %2$s{product_list}%3$s - lista zakupionych produktów
                                                                        %1$s %2$s{amount}%3$s - kwota do zapłaty
                                                                        %1$s %2$s{payment_id}%3$s - numer zamówienia
                                                                        %1$s %2$s{sitename}%3$s - nazwa platformy %1$s',
            ],
            'en_US' => [
                'settings.messages.payment_reminders' => 'Enable the recovery of lost orders',
                'settings.messages.payment_reminders.notice' => 'Change package: To use the lost cart recovery functionality you need to change your license to PRO.',
                'settings.messages.payment_reminders.desc' => 'Enables the mechanism that sends a message to users who have not made a payment.',
                'settings.messages.payment_reminders.number_days' => 'Number of days after which the message is sent in the absence of payment',
                'settings.messages.payment_reminders.message_subject' => 'The subject of the message sent in the event of non-payment',
                'settings.messages.payment_reminders.message_subject.default' => 'You have an unpaid order no  {payment_id}',
                'settings.messages.payment_reminders.message_content' => 'The content of the message sent in the event of non-payment',
                'settings.messages.payment_reminders.message_content.default' => 'Good morning {name},
                                                                        On {date} an order was placed with the obligation to pay for the following products:
                                                                        {product_list}
                                                                        
                                                                        Until now, it has not been paid for by you.
                                                                        To complete the payment, please make a transfer {amount} to the following data:
                                                                        Company name
                                                                        ul. Street name 1
                                                                        00-000 City
                                                                        Bank account number: 00 0000 0000 0000 0000 0000 0000
                                                                        In the title of the transfer, enter: Payment for order no. {Payment_id}
                                                                        
                                                                        Best regards,
                                                                        Team {sitename}',
                'settings.messages.payment_reminders.message_content.desc' => 'Enter the text of the message sent in the event of non-payment. You can use HTML tags in your content and use the following tags:
                                                                        %1$s %2$s{name} %3$s -  buyer name
                                                                        %1$s %2$s{date}%3$s - date of purchase
                                                                        %1$s %2$s{product_list}%3$s - list of purchased products
                                                                        %1$s %2$s{amount}%3$s - amount to pay
                                                                        %1$s %2$s{payment_id}%3$s - order number
                                                                        %1$s %2$s{sitename}%3$s - platform name %1$s',
            ]
        ];
    }
}