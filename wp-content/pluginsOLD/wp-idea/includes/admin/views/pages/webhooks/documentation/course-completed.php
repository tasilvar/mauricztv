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
        "course": $course,
        "full_name": $full_name,
        "email": $email,
        "date_of_the_event": $date_of_the_event
    }
    </pre>
    <strong>Format JSON</strong>
    <br><br>
    <p><?= $translator->translate('webhooks.documentation.description') ?></p>
        <ul class='webhooks-description'>
            <li><span>$course</span>: (string) "Title Course"</li>
            <li><span>$full_name</span>: (string) "Firstname Lastname"</li>
            <li><span>$email</span>: (string) "email@test.pl"</li>
            <li><span>$date_of_the_event</span>: (string) "30.01.2022 - 23:59:00"</li>
        </ul>

            <div class='webhooks-form__footer'>
                <a href='<?= $url_webhook_page ?>' class='webhook-cancel-button'><?= $translator->translate('webhooks.form.return') ?></a>
                <br class='clear' />
            </div>

    
</div>
