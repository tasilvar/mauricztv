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
        "certificate_number": $certificate_number,
        "date_certificate_created": $date_certificate_created
    }
    </pre>
    <strong>Format JSON</strong>
    <br><br>
    <p><?= $translator->translate('webhooks.documentation.description') ?></p>
        <ul class='webhooks-description'>
            <li><span>$course</span>: (string) "Course name"</li>
            <li><span>$full_name</span>: (string) "Firstname Lastname"</li>
            <li><span>$email</span>: (string) "email@test.pl"</li>
            <li><span>$certificate_number</span>: (string) "1423 / 2021"</li>
            <li><span>$date_certificate_created</span>: (string) "2021-11-30"</li>
        </ul>

            <div class='webhooks-form__footer'>
                <a href='<?= $url_webhook_page ?>' class='webhook-cancel-button'><?= $translator->translate('webhooks.form.return') ?></a>
                <br class='clear' />
            </div>
</div>
