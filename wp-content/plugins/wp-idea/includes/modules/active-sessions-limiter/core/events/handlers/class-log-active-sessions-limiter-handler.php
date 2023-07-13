<?php
declare(strict_types=1);

namespace bpmj\wpidea\modules\active_sessions_limiter\core\events\handlers;

use bpmj\wpidea\modules\active_sessions_limiter\core\events\Event_Name;
use bpmj\wpidea\events\Interface_Event_Handler;
use bpmj\wpidea\events\Interface_Events;
use bpmj\wpidea\translator\Interface_Translator;
use bpmj\wpidea\user\Interface_User_Repository;
use bpmj\wpidea\user\User_ID;
use Psr\Log\LoggerInterface;

class Log_Active_Sessions_Limiter_Handler implements Interface_Event_Handler
{
    private Interface_Translator $translator;
    private LoggerInterface $logger;
    private Interface_Events $events;
    private Interface_User_Repository $user_repository;

    public function __construct(
        Interface_Translator $translator,
        LoggerInterface $logger,
        Interface_Events $events,
        Interface_User_Repository $user_repository
    ) {
        $this->translator = $translator;
        $this->logger = $logger;
        $this->events = $events;
        $this->user_repository = $user_repository;
    }

    public function init(): void
    {
        $this->events->on(Event_Name::USER_LOGGED_IN_EXCEEDING_ACTIVE_SESSIONS_LIMIT,  [$this, 'saving_the_log_after_exceeding_active_sessions_limit'], 10, 2);
    }

   public function saving_the_log_after_exceeding_active_sessions_limit(int $user_id, int $destroyed_sessions): void
   {
       $user = $this->user_repository->find_by_id(new User_ID($user_id));

       if(!$user){
           return;
       }

       $this->logger->info(
           $this->translator->translate('logs.log_message.exceeding_active_sessions_limit'), [
           'email' => $user->get_email(),
           'destroyed_sessions' => $destroyed_sessions
       ]);
   }

}