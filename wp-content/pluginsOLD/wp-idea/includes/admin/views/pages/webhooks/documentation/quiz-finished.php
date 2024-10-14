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
        "id": $quiz_id,
        "title": $quiz_title,
        "date": $quiz_finish_date,
        "status": $quiz_status,
        "student": {
            "id": $student_id,
            "first_name": $student_firstname,
            "last_name": $student_lastname,
            "email": $student_email
        }
    }
    </pre>
    <strong>Format JSON</strong>
    <br><br>
    <p><?= $translator->translate('webhooks.documentation.description') ?></p>
    <ul class='webhooks-description'>
        <li><span>$quiz_id</span>: (int) 1</li>
        <li><span>$quiz_title</span>: (string) "Title Quiz"</li>
        <li><span>$quiz_finish_date</span>: (string) "2021-11-30 12:00:00"</li>
        <li><span>$quiz_status</span>: (string) possible values: "passed", "failed"</li>
        <li><span>$student_id</span>: (int) 2</li>
        <li><span>$student_firstname</span>: (string) "Firstname"</li>
        <li><span>$student_lastname</span>: (string) "Lastname"</li>
        <li><span>$student_email</span>: (string) "email@test.pl"</li>
    </ul>

    <div class='webhooks-form__footer'>
        <a href='<?= $url_webhook_page ?>' class='webhook-cancel-button'><?= $translator->translate('webhooks.form.return') ?></a>
        <br class='clear' />
    </div>


</div>
